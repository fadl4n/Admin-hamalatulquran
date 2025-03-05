<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Santri;
use App\Models\Pengajar;

class AuthController extends Controller
{
    public function doLogin(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'identifier' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // Cek apakah identifier itu NIP (Pengajar) atau NISN (Santri)
        $pengajar = Pengajar::where('nip', $request->identifier)->first();
        $santri = Santri::where('nisn', $request->identifier)->first();

        $user = $pengajar ?: $santri;
        $role = $pengajar ? 'pengajar' : ($santri ? 'santri' : null);

        if ($user && Hash::check($request->password, $user->password)) {
            // TOKEN PAYLOAD
            $payload = [
                'iss' => "hamalatulquran-app", // Issuer
                'sub' => $user->id, // User ID
                'role' => $role, // Role
                'iat' => time(), // Issued at
                'exp' => time() + 60 * 60 * 24, // Expiration (24 jam)
            ];

            // Encode token dengan secret key dari .env
            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            return response()->json([
                'success' => true,
                'message' => 'Login success',
                'data' => [
                    'id' => $user->{$pengajar ? 'id_pengajar' : 'id_santri'},
                    'nama' => $user->nama,
                    'role' => $role,
                ],
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid identifier or password',
        ], 400);
    }

    public function profile($identifier)
    {
        if (!$identifier) {
            return response()->json([
                'status' => 'error',
                'message' => 'Identifier diperlukan'
            ], 400); // 400 Bad Request
        }

        // Cek di tabel Pengajar (pakai NIP)
        $pengajar = Pengajar::where('id_pengajar', $identifier)->orWhere('nip', $identifier)->first();
        if ($pengajar) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $pengajar->id_pengajar,
                    'identifier' => $identifier,
                    'nama' => $pengajar->nama,
                    'nip' => $pengajar->nip,
                    'tempat_tanggal_lahir' => $pengajar->tempat_lahir . ', ' . date('d M Y', strtotime($pengajar->tgl_lahir)),
                    'jenis_kelamin' => $pengajar->jenis_kelamin,
                    'no_telp' => $pengajar->no_telp,
                    'alamat' => $pengajar->alamat,
                    'role' => 'pengajar'
                ]
            ]);
        }

        // Cek di tabel Santri (pakai NISN)
        $santri = Santri::where('id_santri', $identifier)->orWhere('nisn', $identifier)->first();
        if ($santri) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $santri->id_santri,
                    'identifier' => $identifier,
                    'nama' => $santri->nama,
                    'nisn' => $santri->nisn,
                    'tempat_tanggal_lahir' => $santri->tempat_lahir . ', ' . date('d M Y', strtotime($santri->tgl_lahir)),
                    'jenis_kelamin' => $santri->jenis_kelamin,
                    'no_telp' => $santri->no_telp,
                    'alamat' => $santri->alamat,
                    'kelas' => $santri->id_kelas,
                    'role' => 'santri'
                ]
            ]);
        }

        // Kalau user gak ditemukan
        return response()->json([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ], 404);
    }


    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  =>  'required',
            'name'  =>  'required',
            'phone'  =>  'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $param = $request->only('email', 'name', 'phone');

        // image handling
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $allowedFileTypes = ['png', 'jpg', 'jpeg'];
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, $allowedFileTypes)) {
                return redirect()->back()->with('error', 'File type not allowed. Only png and jpg files are allowed.');
            }

            $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();
            $filenames[] = $name_original;
            $filesizes[] = $file->getSize();
            $file->move(public_path('uploadedFile/image/user'), $name_original);
            $files = url('uploadedFile/image/user') . '/' . $name_original;

            $param['image'] = $files;
        } else {
            $param['image'] = asset('assets/image/default-user.png');
        }

        $data = User::where('id', $request->userData->id)->update($param);

        if ($data) {
            $user = User::where('id', $request->userData->id)->first();
            return response()->json([
                'success' => true,
                'message' => 'Profile updated',
                'data' => $data,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'data' => (object) array(),
        ], 500);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'  =>  'required',
            'new_password'  =>  'required',
            'new_password_confirmation'  =>  'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $data = User::where('id', $request->userData->id)->first();

        if (Hash::check($request->old_password, $data->password)) {
            $param['password'] = Hash::make($request->new_password);
            $update = User::where('id', $request->userData->id)->update($param);

            if ($update) {
                $user = User::where('id', $request->userData->id)->first();
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated',
                    'data' => $data,
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => (object) array(),
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid old password',
            'data' => (object) array(),
        ], 400);
    }
}
