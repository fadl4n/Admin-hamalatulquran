<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function doLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  =>  'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->errors()->first());
            return redirect()->back();
        }
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // CHECK PASSWORD
            if (Hash::check($request->password, $user->password)) {

                // TOKEN PAYLOAD
                $payload = [
                    'iss' => "member-service", // Issuer of the token
                    'sub' => $user->id, // Subject of the token
                    'iat' => time(), // Time when JWT was issued.
                    'exp' => time() + 60 * 60 * 24,
                    'scope' => 'development',
                    'platform' => 'cms',
                    'data' => [
                        'id' => $user->id,
                        'scope' => 'development',
                        'platform' => 'cms',
                    ]
                ];
                $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

                // SET SESSION
                $request->session()->put('token', $token);
                $request->session()->put('user',
                [
                    'id' => $user->id,
                    'role_id' => $user->role_id,
                    'name' => $user->name,
                    'image' => $user->image,
                ]);

                return redirect('/');
            }
        }

        Session::flash('error', 'Email or password is incorrect');

        return redirect()->back();
    }

    public function logout (){
        Session::flush();
        return redirect('/login');
    }

    public function profile() {
        $data = User::where('id', Session::get('user')['id'])->first();
        return view('profile', ['data' => $data]);
    }


    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passwordConfirmation' => 'same:password',
        ]);
        if ($validator->fails()) {
            Session::flash('error', $validator->errors()->first());
            return redirect()->back();
        }

        $param = $request->except('_token', 'password', 'passwordConfirmation', 'image');

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

        if ($request->password != null) {
            $param['password'] = Hash::make($request->password);
        }

        $data = User::where('id', Session::get('user')['id'])->update($param);

        if ($data) {
            $user = User::where('id', Session::get('user')['id'])->first();
            Session::flash('success', 'Data Updated');
            $request->session()->put('user',
            [
                'id' => $user->id,
                'role_id' => $user->userRoleid,
                'name' => $user->name,
                'image' => $user->image,
            ]);

            return redirect('/profile');
        }

        Session::flash('error', 'Data not updated');

        return redirect('/profile');
    }

}
