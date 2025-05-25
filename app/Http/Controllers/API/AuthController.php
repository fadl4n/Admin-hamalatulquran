<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
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
        // return $pengajar;

        // Cek apakah identifier itu NIP (Pengajar) atau NISN (Santri)
        $pengajar = Pengajar::where('nip', $request->identifier)->first();
        $santri = Santri::where('nisn', $request->identifier)->first();

        if ($pengajar && Hash::check($request->password, $pengajar->password)) {
            $user = $pengajar;
            $role = 'pengajar';
            $user_id = $user->id_pengajar;
            $foto_profil = $user->foto_pengajar ? url($user->foto_pengajar) : null;
            $jenis_kelamin = $user->jenis_kelamin;
        } elseif ($santri && Hash::check($request->password, $santri->password)) {
            $user = $santri;
            $role = 'santri';
            $user_id = $user->id_santri;
            $foto_profil = $user->foto_santri ? url($user->foto_santri) : null;
            $jenis_kelamin = $user->jenis_kelamin;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid identifier or password',
            ], 400);
        }

        // dd(env('JWT_SECRET'));
        // Encode token
        $key = env('JWT_SECRET');
        // var_dump($key);
        // TOKEN PAYLOAD
        $payload = [
            'iss' => "hamalatulquran-app",
            'sub' => $user_id, // Pakai user_id yang ada prefix
            'role' => $role,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24,
        ];

        try {
            // $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
            $token = JWT::encode($payload, $key, 'HS256');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'JWT encode error: ' . $e->getMessage()], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => [
                'id' => $user_id, // Kirim ID dengan prefix
                'nama' => $user->nama,
                'role' => $role,
                'foto_profil' => $foto_profil,
                'jenis_kelamin' => $jenis_kelamin
            ],
            'token' => $token,
        ], 200);
    }

    public function profile($role, $identifier, Request $request)
    {
        // Cek apakah token ada di header
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        try {
            // Decode token untuk validasi
            $decoded = JWT::decode(str_replace("Bearer ", "", $token), new Key(env('JWT_SECRET'), 'HS256'));

            Log::info("Mencari $role dengan ID: $identifier");

            // Cek berdasarkan role
            if ($role === 'pengajar') {
                $pengajar = Pengajar::where('id_pengajar', $identifier)->first();

                if (!$pengajar) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Pengajar tidak ditemukan'
                    ], 404);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id' => $pengajar->id_pengajar,
                        'nama' => $pengajar->nama,
                        'nip' => $pengajar->nip,
                        'tempat_tanggal_lahir' => $pengajar->tempat_lahir . ', ' . date('d M Y', strtotime($pengajar->tgl_lahir)),
                        'jenis_kelamin' => $pengajar->jenis_kelamin,
                        'no_telp' => $pengajar->no_telp,
                        'alamat' => $pengajar->alamat,
                        'role' => 'pengajar',
                        'foto_profil' => !empty($pengajar->foto_pengajar) ? url($pengajar->foto_pengajar) : "" // ✅ Pastikan selalu String
                    ]
                ]);
            }

            if ($role === 'santri') {
                $santri = Santri::where('id_santri', $identifier)->first();

                if (!$santri) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Santri tidak ditemukan'
                    ], 404);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id' => $santri->id_santri,
                        'nama' => $santri->nama,
                        'nisn' => $santri->nisn,
                        'tempat_tanggal_lahir' => $santri->tempat_lahir . ', ' . date('d M Y', strtotime($santri->tgl_lahir)),
                        'jenis_kelamin' => $santri->jenis_kelamin,
                        'no_telp' => $santri->no_telp,
                        'alamat' => $santri->alamat,
                        'kelas' => $santri->id_kelas,
                        'role' => 'santri',
                        'foto_profil' => !empty($santri->foto_santri) ? url($santri->foto_santri) : "" // ✅ Pastikan selalu String
                    ]
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid: ' . $e->getMessage()
            ], 401);
        }
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
