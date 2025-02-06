<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function doLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  =>  'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }
        $data = User::where('email', $request->email)->first();

        if ($data) {
            // CHECK PASSWORD
            if (Hash::check($request->password, $data->password)) {

                // TOKEN PAYLOAD
                $payload = [
                    'iss' => "member-service", // Issuer of the token
                    'sub' => $data->id, // Subject of the token
                    'iat' => time(), // Time when JWT was issued.
                    'exp' => time() + 60 * 60 * 24,
                    'scope' => 'development',
                    'platform' => 'frontend',
                    'data' => [
                        'id' => $data->id,
                        'scope' => 'development',
                        'platform' => 'frontend',
                    ]
                ];
                $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

                return response()->json([
                    'success' => true,
                    'message' => 'Login success',
                    'data' => $data,
                    'token' => $token,
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password',
            'data' => (object) array(),
        ], 400);
    }

    public function profile(Request $request) {
        $data = User::where('id', $request->userData->id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $data,
        ], 200);
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

            $name_original = date('YmdHis').'_'.$file->getClientOriginalName();
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
