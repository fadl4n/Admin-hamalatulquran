<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function fnGetTotalUser(Request $request) {
        $data = User::count();
        return response()->json($data, 200);
    }
}
