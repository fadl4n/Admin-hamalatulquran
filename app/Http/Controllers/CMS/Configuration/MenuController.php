<?php

namespace App\Http\Controllers\CMS\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use DataTables;
use App\Models\GroupMenu;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Priviledge;

class MenuController extends Controller
{
    public function index()
    {
        return view('menu/show');
    }

    public function create()
    {
        $menuGroups = GroupMenu::all();
        return view('menu/create', ['menuGroups' => $menuGroups]);
    }

    public function store(Request $request)
    {
        $param = $request->except('_token');

        $data = Menu::create($param);

        if ($data) {
            $roles = Role::all();
            foreach ($roles as $key => $role) {
                $paramPriviledge['role_id'] = $role->id;
                $paramPriviledge['menu_id'] = $data->id;
                $paramPriviledge['view'] = 0;
                $paramPriviledge['add'] = 0;
                $paramPriviledge['edit'] = 0;
                $paramPriviledge['delete'] = 0;
                $paramPriviledge['other'] = 0;
                if ($role->id == 1) {
                    $paramPriviledge['view'] = 1;
                    $paramPriviledge['add'] = 1;
                    $paramPriviledge['edit'] = 1;
                    $paramPriviledge['delete'] = 1;
                    $paramPriviledge['other'] = 1;
                }
                Priviledge::create($paramPriviledge);
            }
            Session::flash('success', 'Data Created');
            return redirect('/menu');
        }

        Session::flash('error', $data->message);

        return redirect()->back();
    }

    public function edit($id)
    {
        $data = Menu::where('id', $id)->first();
        $menuGroups = GroupMenu::all();
        return view('menu/edit', ['data' => $data, 'menuGroups' => $menuGroups]);
    }

    public function update(Request $request)
    {
        $param = $request->except('_token');
        
        $data = Menu::where('id', $request->id)->update($param);

        if ($data) {
            Session::flash('success', 'Data Updated');
            return redirect('/menu');
        }

        Session::flash('error', $data->message);

        return redirect()->back();
    }

    public function delete($id)
    {
        $data = Menu::where('id', $id)->delete();
        
        if ($data) {
            Session::flash('success', 'Data Deleted');
            return redirect('/menu');
        } else {
            Session::flash('error', $data->message);

            return redirect()->back();
        }
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
            $sort = $request->input('order')[0]['dir'];
        }
        $data = Menu::with('groupMenu')->orderBy($sort,$sortBy);

        if ($request->has('limit') && $request->limit != '') {
            $limit = $request->limit;
        }

        if ($request->has('search') && $request->search['value'] != '') {
            $data = $data->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->search['value'].'%');
                $q->orWhere('url','like','%'.$request->search['value'].'%');
                $q->orWhere('sequence','like','%'.$request->search['value'].'%');
            });
        }

        $data = $data->paginate($limit)->toJson();
        $data = json_decode($data);
        if(isset($data)){
            return DataTables::of($data->data)
            ->skipPaging()
            ->setTotalRecords($data->total)
            ->setFilteredRecords($data->total)
            ->addColumn('action', function ($q) {
                $btn = '<a href="'. url('menu/edit/' . $q->id).'" class="text-warning btnEdit"><i class="fas fa-edit"></i></a>';
                $btn .= '| <a href="#" data-id="'.$q->id.'" class="text-red btnDelete"><i class="fas fa-trash-alt"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        
        return false;
    }
}
