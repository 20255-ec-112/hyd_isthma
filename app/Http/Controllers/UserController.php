<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataTables $datatable)
    {
        if (auth()->user()->id != 1 && !auth()->user()->hasPermissionTo('View Users')){
            abort(403);
        }
        if($request->ajax()) {
            $query = User::query()->where('id', '!=', 1);
            return $datatable->eloquent($query)
                ->addColumn('role', function (User $user) {
                    return $user->getRoleNames()->first();
                })
                ->addColumn('action', function (User $user) {
                    $link = (auth()->user()->id == 1 || auth()->user()->hasPermissionTo('Edit Users')) ?
                        '<a href="' . route('user.edit', $user->id) . '" class="btn-purple btn mr-1" ><i class="fas fa-edit"></i></a>'
                        : "";
                    $link .= auth()->user()->id == 1 || auth()->user()->hasPermissionTo('Delete Users') ?
                            '<span data-href="'.route('user.destroy', $user->id).'" class="btn-purple user-delete btn"><i class="fas fa-trash"></i></span>'
                            : "";
                    return $link;
                })
                ->rawColumns([ 'role', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.users.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('Create Users')){
            abort(403);
        }

        $roles = Role::all();
        return view('admin.users.form')->with([
            'roles' => $roles,
            'user' => new User(),
            'user_role' => '',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required',

        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        $user->assignRole($request->role);
        return redirect()->route('user.index')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $loggedInUser = User::find(auth()->user()->id);
        if ($loggedInUser->id != 1 && !$loggedInUser->hasPermissionTo('Edit Users')){
            abort(403);
        }

        $roles = Role::all();
        return view('admin.users.form')->with([
            'roles' => $roles,
            'user' =>  $user,
            'user_role' => $user->getRoleNames()->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
        ]);
        $data['zone_name'] = $request->get('zone_name');
        if(!empty($request->input('password'))) {
            $request->validate([
                'password' => 'min:8',
            ]);
            $data['password'] = bcrypt($request->input('password'));
        }
        $user = User::find($id);
        $user->update($data);
        $user->syncRoles([$request->role]);
        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('Delete Users')){
            abort(403);
        }
        $user = User::where('id', $id)->first();
        $user->delete();
        return response()->noContent();
    }
}
