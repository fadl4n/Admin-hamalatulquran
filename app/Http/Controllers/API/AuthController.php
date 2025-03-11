<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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

        if ($pengajar && Hash::check($request->password, $pengajar->password)) {
            $user = $pengajar;
            $role = 'pengajar';
            $user_id = "pengajar_" . $user->id_pengajar;
        } elseif ($santri && Hash::check($request->password, $santri->password)) {
            $user = $santri;
            $role = 'santri';
            $user_id = "santri_" . $user->id_santri;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid identifier or password',
            ], 400);
        }

        // TOKEN PAYLOAD
        $payload = [
            'iss' => "hamalatulquran-app",
            'sub' => $user_id, // Pakai user_id yang ada prefix
            'role' => $role,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24,
        ];

        // Encode token
        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => [
                'id' => $user_id, // Kirim ID dengan prefix
                'nama' => $user->nama,
                'role' => $role,
            ],
            'token' => $token,
        ], 200);
    }

    public function profile(Request $request, $identifier)
    {
        // Ambil role dari query parameter
        $role = $request->query('role');

        // Cek apakah role valid
        if (!in_array($role, ['pengajar', 'santri'])) {
            Log::error("Role tidak valid: $role");
            return response()->json([
                'status' => 'error',
                'message' => 'Role tidak valid'
            ], 400);
        }

        Log::info("Mencari $role dengan ID: $identifier");

        if ($role === 'pengajar') {
            Log::info("Query: mencari pengajar dengan id_pengajar = $identifier");
            $pengajar = Pengajar::where('id_pengajar', $identifier)->first();

            if ($pengajar) {
                Log::info("Pengajar ditemukan:", ['data' => $pengajar]);
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id' => "pengajar_" . $pengajar->id_pengajar,
                        'nama' => $pengajar->nama,
                        'nip' => $pengajar->nip,
                        'tempat_tanggal_lahir' => $pengajar->tempat_lahir . ', ' . date('d M Y', strtotime($pengajar->tgl_lahir)),
                        'jenis_kelamin' => $pengajar->jenis_kelamin,
                        'no_telp' => $pengajar->no_telp,
                        'alamat' => $pengajar->alamat,
                        'role' => 'pengajar'
                    ]
                ]);
            } else {
                Log::error("Pengajar tidak ditemukan dengan ID: $identifier");
            }
        } elseif ($role === 'santri') {
            Log::info("Query: mencari santri dengan id_santri = $identifier");
            $santri = Santri::where('id_santri', $identifier)->first();

            if ($santri) {
                Log::info("Santri ditemukan:", ['data' => $santri]);
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id' => "santri_" . $santri->id_santri,
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
            } else {
                Log::error("Santri tidak ditemukan dengan ID: $identifier");
            }
        }

        Log::error("User tidak ditemukan untuk role $role dengan ID: $identifier");

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
