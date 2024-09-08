<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelperFunctions;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class MembersController extends Controller
{
    public function index(Request $request, DataTables $dataTables)
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View Members')){
            abort(403);
        }
        if ($request->ajax()) {
            $query = Member::query()->filterByZone()->orderBy('name', 'asc');
            // filters for registered/non-registered
            if ($request->has('register_noregister') && $request->register_noregister !== '') {
                if ($request->register_noregister == 'registered') {
                    $query->whereIn('members.id', function($query) {
                        $query->select('member_id')->from('registrations');
                    });
                } elseif ($request->register_noregister == 'non-registered') {
                    $query->whereNotIn('members.id', function($query) {
                        $query->select('member_id')->from('registrations');
                    });
                }
            }
            if (!empty($request->unit_name)) {
                    $query->where('unit_name', $request->unit_name);
                }
            if (!empty($request->zone_name)) {
                $query->where('zone_name', $request->zone_name);
            }
            if (!empty($request->division_name)) {
                $query->where('division_name', $request->division_name);
            }
            $query->select('members.*')->orderBy('name', 'asc');

            return $dataTables->eloquent($query)
                ->editColumn('dob', function (Member $member) {
                    return date('d-m-Y', strtotime($member->dob));
                })
                ->addColumn('reg_status', function (Member $member) {
                    return $member->registration ? AppHelperFunctions::getGreenBadge('Registered') : AppHelperFunctions::getRedBadge('Not Registered');
                })
                ->addColumn('action', function (Member $member) use($user) {
                    return $user->id == 1 || $user->hasPermissionTo('Edit Members') ? '<a href="' . route('members.edit', $member->id) . '" class="btn btn-sm btn-purple btn-clean btn-icon" title="Edit"><i class="fas fa-edit"></i></a>' : '';
                })
                ->rawColumns(['dob', 'reg_status' ,'action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.members.list');
    }


    public function create()
    {
        $user = User::find(auth()->user()->id);
        if( $user->id != 1 && ! $user->hasPermissionTo('Create Members')) {
            abort(403);
        }
        return view('admin.members.form')->with([
            'member' => new Member()
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'zone_name' => 'required',
            'unit_name' => 'required',
            'division_name' => 'required'
        ]);

        $member = Member::create($request->all());
        $member->update(
                [
                    'status' => $member->status === 'on' ? 'Active' : 'Inactive',
                    'dob' => date('Y-m-d', strtotime($request->dob)),
                    'age' => $this->getAgeFromDOB($request->dob)
                ]);

        return redirect()->route('members.index');
    }

    public function show(string $id)
    {

    }

    public function edit(Member $member)
    {
        $user = User::find(auth()->user()->id);
        if( $user->id != 1 && ! $user->hasPermissionTo('Edit Members')) {
            abort(403);
        }

        $member->dob = date('d-m-Y', strtotime($member->dob));
        // $member = Member::find($id);
        return view('admin.members.form')->with([
            'member' => $member
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $this->validate($request, [
            'name' => 'required',
            'zone_name' => 'required',
            'unit_name' => 'required',
            'division_name' => 'required',
            'phone' =>  'required'
        ]);
        $updateData = [
            "name" => $request->name,
            "email" => $request->email,
            "zone_name" => $request->zone_name,
            "unit_name" => $request->unit_name,
            "division_name" => $request->division_name,
            "status" => $request->status === 'on' ? 'Active' : 'InActive',
            "dob" => date('Y-m-d', strtotime($request->dob)),
            "user_number" => $request->user_number,
            "gender" => $request->gender,
            "phone" => $request->phone,
            "age" => $this->getAgeFromDOB($request->dob)
        ];

        $member->update($updateData);
        return redirect()->route('members.index');
    }

    public function destroy(string $id)
    {
        //
    }

    public function getAgeFromDOB($dob) {
        if (!empty($dob)) {
            if(Str::contains($dob, '/'))
                $dob = str_replace('/', '-', $dob);
            return  Carbon::parse($dob)->age;
        }
        return null;
    }
}