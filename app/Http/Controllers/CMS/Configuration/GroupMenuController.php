<?php

namespace App\Http\Controllers\CMS\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Models\GroupMenu;

class GroupMenuController extends Controller
{
    public function index()
    {
        return view('group-menu/show');
    }

    public function create()
    {
        return view('group-menu/create');
    }

    public function store(Request $request)
    {
        $param = $request->except('_token');

        $data = GroupMenu::create($param);

        if ($data) {
            Session::flash('success', 'Data Created');
            return redirect('/group-menu');
        }

        Session::flash('error', $data->message);

        return redirect()->back();
    }

    public function edit($id)
    {
        $data = GroupMenu::where('id', $id)->first();
        return view('group-menu/edit', ['data' => $data]);
    }

    public function update(Request $request)
    {
        $param = $request->except('_token');

        $data = GroupMenu::where('id', $request->id)->update($param);

        if ($data) {
            Session::flash('success', 'Data Updated');
            return redirect('/group-menu');
        }

        Session::flash('error', $data->message);

        return redirect()->back();
    }

    public function delete($id)
    {
        $data = GroupMenu::where('id', $id)->delete();

        if ($data) {
            Session::flash('success', 'Data Deleted');
            return redirect('/group-menu');
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
        $data = GroupMenu::orderBy($sort,$sortBy);

        if ($request->has('limit') && $request->limit != '') {
            $limit = $request->limit;
        }

        if ($request->has('search') && $request->search['value'] != '') {
            $data = $data->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->search['value'].'%');
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
                $btn = '<a href="'. url('group-menu/edit/' . $q->id).'" class="text-warning btnEdit"><i class="fas fa-edit"></i></a>';
                $btn .= '| <a href="#" data-id="'.$q->id.'" class="text-red btnDelete"><i class="fas fa-trash-alt"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return false;
    }
}
