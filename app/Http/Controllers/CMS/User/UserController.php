<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use DataTables;
use File;
use Validator;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('user/show');
    }

    public function create()
    {
        return view('user/create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'passwordConfirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->errors()->first());
            return redirect()->back();
        }
        $param = $request->except('_token', 'passwordConfirmation');
        $param['password'] = Hash::make($param['password']);

        $data = User::create($param);

        if ($data) {
            return redirect('/users')->with('success', 'Data Created');
        }
        Session::flash('error', 'Something went wrong');

        return redirect()->back();
    }

    public function detail($id)
    {
        $data = User::where('id', $id)->with('role')->first();

        return view('user/detail', ['data' => $data]);
    }

    public function edit($id)
    {
        $data = User::where('id', $id)->with('role')->first();

        return view('user/edit', ['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'passwordConfirmation' => 'same:password',
        // ]);
        // if ($validator->fails()) {
        //     Session::flash('error', $validator->errors()->first());
        //     return redirect()->back();
        // }

        $param = $request->except('_token');
        // if ($param['password'] == null || $param['password'] == '') {
        //     unset($param['password']);
        // } else {
        //     $param['password'] = Hash::make($param['password']);
        // }

        $data = User::where('id', $id)->update($param);

        if ($data) {
            return redirect('/users')->with('success', 'Data Updated');
        }
        Session::flash('error', 'Something went wrong');

        return redirect()->back();
    }

    public function delete($id)
    {
        $data = User::where('id', $id)->delete();
        if ($data) {
            return redirect('/users')->with('success', 'Data Deleted');
        }
        Session::flash('error', 'Something went wrong');

        return redirect()->back();
    }

    public function fnGetData(Request $request, DataTables $datatable)
    {   
        $edit = true;
        $delete = true;
        $page = ($request->start / $request->length) + 1;
        $limit = 10;
        $sort = 'id';
        if ($request->has('order') && $request->input('order')[0]['column'] != 0) {
            $sort = $request->input('columns')[$request->input('order')[0]['column']]['data'];
        }
        $sortBy = 'asc';
        if ($request->has('order') && $request->input('order')[0]['column'] != 0) {
            $sortBy = $request->input('order')[0]['dir'];
        }
        $data = User::orderBy($sort,$sortBy)->with('role');

        if ($request->has('limit') && $request->limit != '') {
            $limit = $request->limit;
        }

        if ($request->has('search') && $request->search['value'] != '') {
            $data = $data->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->search['value'].'%');
                $q->orWhere('email','like','%'.$request->search['value'].'%');
                $q->orWhere('phone','like','%'.$request->search['value'].'%');
                $q->orWhereHas('role', function ($q2) use($request) {
                    $q2->where('name', 'LIKE', '%'.$request->search['value'].'%');
                });
            });
        }

        $data = $data->paginate($limit)->toJson();
        $data = json_decode($data);
        if(isset($data)){
            return DataTables::of($data->data)
            ->skipPaging()
            ->setTotalRecords($data->total)
            ->setFilteredRecords($data->total)
            ->addIndexColumn()
            ->addColumn('role_table', function ($q) {
                if (isset($q->role)) {
                    return $q->role->name;
                }
                return '';
            })
            ->addColumn('action', function ($q) {
                $btn = '<a href="'. url('users/detail/'. $q->id).'" class="btn btn-sm btn-info text-white btnDetail"><i class="fas fa-eye"></i></a>';
                $btn .= ' <a href="' . url('users/edit/' . $q->id) . '" class="btn btn-sm btn-light text-navy"><i class="fas fa-edit"></i></a>';
                $btn .= ' <a href="#" data-id="'.$q->id.'" class="btn btn-sm btn-danger text-white btnDelete"><i class="fas fa-trash-alt"></i></a>';
                return $btn;
            })
            ->rawColumns(['action', 'role_table'])
            ->make(true);
        }
        
        return false;
    }
}
