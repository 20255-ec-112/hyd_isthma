<?php

namespace App\Http\Controllers;

use App\Models\SessionTheme;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SessionThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataTables $datatable)
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View SessionThemes')){
            abort(403);
        }
        if($request->ajax()) {
            $query = SessionTheme::query();
            return $datatable->eloquent($query)
                ->editColumn('status', function (SessionTheme $theme) {
                    return $theme->status ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">In Active</span>';
                })
                ->addColumn('action', function (SessionTheme $theme) use($user) {
                    $link = ($user->id == 1 || $user->hasPermissionTo('Edit SessionThemes')) ?
                        '<a href="' . route('sessiontheme.edit', $theme->id) . '" class="btn-purple btn mr-1" ><i class="fas fa-edit"></i></a>'
                        : "";
                    return $link;
                })
                ->rawColumns([ 'status', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.sessiontheme.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('Create SessionThemes')){
            abort(403);
        }
        return view('admin.sessiontheme.form')->with([
            'theme' => new SessionTheme(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'theme_name' => 'required',
            'theme_type' => 'required',
            'status' => 'required',
        ]);
        $theme = new SessionTheme();
        $theme->theme_name = $request->theme_name;
        $theme->theme_type = $request->theme_type;
        $theme->status = $request->status;
        $theme->save();
        return redirect()->route('sessiontheme.index')->with('success', 'Theme Session created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(SessionTheme $sessionTheme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $loggedInUser = User::find(auth()->user()->id);
        if ($loggedInUser->id != 1 && !$loggedInUser->hasPermissionTo('Edit SessionThemes')){
            abort(403);
        }
        return view('admin.sessiontheme.form')->with([
           'theme' => SessionTheme::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        $sessionTheme = SessionTheme::find($id);
        $data = $request->validate([
            'theme_name' => 'required',
            'theme_type' => 'required',
            'status' => 'required',
        ]);
        $sessionTheme->update($data);
        return redirect()->route('sessiontheme.index')->with('success', 'Theme session updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SessionTheme $sessionTheme)
    {
        //
    }
}
