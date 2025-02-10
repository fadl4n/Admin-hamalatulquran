<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use App\Models\Role;
use App\Models\Priviledge;
use App\Models\Menu;


class RoleController extends Controller
{
    public function index()
    {
        return view('roles/show');
    }

    public function create()
    {
        $menus = Menu::with('groupMenu')->get();
        return view('roles/create', ['menus' => $menus]);
    }

    public function store(Request $request)
    {
        $param = $request->only('name', 'description');

        $data = Role::create($param);

        if ($data) {
            $menus = Menu::all();
            foreach ($menus as $key => $menu) {
                $paramPriviledge['role_id'] = $data->id;
                $paramPriviledge['menu_id'] = $menu->id;
                if($request->has('view_'.$menu->id)){
                    $paramPriviledge['view'] = 1;
                } else {
                    $paramPriviledge['view'] = 0;
                }

                if($request->has('add_'.$menu->id)){
                    $paramPriviledge['add'] = 1;
                } else {
                    $paramPriviledge['add'] = 0;
                }

                if($request->has('edit_'.$menu->id)){
                    $paramPriviledge['edit'] = 1;
                } else {
                    $paramPriviledge['edit'] = 0;
                }

                if($request->has('delete_'.$menu->id)){
                    $paramPriviledge['delete'] = 1;
                } else {
                    $paramPriviledge['delete'] = 0;
                }

                if($request->has('other_'.$menu->id)){
                    $paramPriviledge['other'] = 1;
                } else {
                    $paramPriviledge['other'] = 0;
                }
                Priviledge::create($paramPriviledge);
            }
            Session::flash('success', 'Data Created');
            return redirect('/roles');
        }
        Session::flash('error', 'Something went wrong');

        // Redirect the user back to the previous page
        return redirect()->back();
    }

    public function edit($id)
    {
        $data = Role::where('id', $id)->with('priviledge')->first();
        foreach ($data->priviledge as $key => $priviledge) {
            $remapPriviledge['view_'.$priviledge->menu_id] = $priviledge->view;
            $remapPriviledge['add_'.$priviledge->menu_id] = $priviledge->add;
            $remapPriviledge['edit_'.$priviledge->menu_id] = $priviledge->edit;
            $remapPriviledge['delete_'.$priviledge->menu_id] = $priviledge->delete;
            $remapPriviledge['other_'.$priviledge->menu_id] = $priviledge->other;
            $data->remapPriviledge = $remapPriviledge;
        }
        $menus = Menu::with('groupMenu')->get();
        return view('roles/edit', ['data' => $data, 'menus' => $menus]);
    }

    public function update(Request $request, $id)
    {
        $param = $request->only('name', 'description');

        $data = Role::where('id', $id)->update($param);

        if ($data) {
            $menus = Menu::all();
            foreach ($menus as $key => $menu) {
                if($request->has('view_'.$menu->id)){
                    $paramPriviledge['view'] = 1;
                } else {
                    $paramPriviledge['view'] = 0;
                }

                if($request->has('add_'.$menu->id)){
                    $paramPriviledge['add'] = 1;
                } else {
                    $paramPriviledge['add'] = 0;
                }

                if($request->has('edit_'.$menu->id)){
                    $paramPriviledge['edit'] = 1;
                } else {
                    $paramPriviledge['edit'] = 0;
                }

                if($request->has('delete_'.$menu->id)){
                    $paramPriviledge['delete'] = 1;
                } else {
                    $paramPriviledge['delete'] = 0;
                }

                if($request->has('other_'.$menu->id)){
                    $paramPriviledge['other'] = 1;
                } else {
                    $paramPriviledge['other'] = 0;
                }
                Priviledge::where('role_id', $id)->where('menu_id', $menu->id)->update($paramPriviledge);
            }
            Session::flash('success', 'Data Updated');
            return redirect('/roles');
        }
        Session::flash('error', 'Data not found');

        // Redirect the user back to the previous page
        return redirect()->back();
    }

    public function delete($id)
    {
        $data = Role::where('id', $id)->delete();
        if ($data) {
            Priviledge::where('role_id', $id)->delete();
            Session::flash('success', 'Data Deleted');
            return redirect('/roles');
        }

        Session::flash('error', 'Data not found');
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
        $data = Role::orderBy($sort,$sortBy);

        if ($request->has('limit') && $request->limit != '') {
            $limit = $request->limit;
        }

        if ($request->has('search') && $request->search['value'] != '') {
            $data = $data->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->search['value'].'%');
                $q->orWhere('description','like','%'.$request->search['value'].'%');
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
            ->addColumn('action', function ($q) {
                $btn = ' <a href="' . url('roles/edit/' . $q->id) . '" class="btn btn-sm btn-light text-navy"><i class="fas fa-edit"></i></a>';
                $btn .= ' <a href="#" data-id="'.$q->id.'" class="btn btn-sm btn-danger text-white btnDelete"><i class="fas fa-trash-alt"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return false;
    }
}
