<?php

namespace App\Http\Controllers\CMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Role;

class RoleController extends Controller
{    
    public function __construct()
    {
        
    }

    /**
     * fnAutoComplete
     * 
     * @param Request $request
     * @return view
     */

    public function index(Request $request)
    {
        $page = $request->input('page');
        $data = Role::orderBy('id','asc');

        if ($request->has('term') && $request->term != '') {
            $data = $data->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->term.'%');
            });
        }

        $data = $data->paginate(10)->toJson();
        $data = json_decode($data);
        
        $morePages = true;
        if ($data->current_page == $data->last_page) {
            $morePages = false;
        }
        
        return response()->json([
            'results' => isset($data->data)?$data->data:[],
            'pagination' => array("more" => $morePages),
        ]);
    }
}
