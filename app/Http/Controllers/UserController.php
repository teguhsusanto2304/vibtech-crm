<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use app\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('user',compact('user'))->with('title', 'Dashboard')->with('breadcrumb', ['Home','Dashboard']);
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
