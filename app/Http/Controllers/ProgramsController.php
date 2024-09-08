<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramSpeaker;
use App\Models\SessionTheme;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataTables $datatable)
    {
         $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View Programs')){
            abort(403);
        }
        if($request->ajax()) {
            $query = Program::with('programSpeaker', 'sessionTheme');
            return $datatable->eloquent($query)
                ->editColumn('datetime', function (Program $program) {
                   return $program->datetime ?
                        '<span class="badge badge-success">' . date('Y-m-d h:m:A', strtotime($program->datetime)) . '</span>' : 'NA';
                })
                ->addColumn('session_theme', function (Program $program) {
                    return '<a href="' . route('sessiontheme.edit', $program->session_theme_id) . '">' . $program->sessionTheme->theme_name . '</a>';
                })
                ->addColumn('speaker_name', function (Program $program) {
                    return '<a href="' . route('programSpeakers.edit', $program->program_speaker_id) . '">' . $program->programSpeaker->name . '</a>';
                })
                ->addColumn('speaker_image', function (Program $program) {
                    $imageSrc = !empty($program->programSpeaker->getMedia('speaker_image')->first()) ? $program->programSpeaker->getMedia('speaker_image')->first()->getUrl() : '/assets/img/no-image.png';
                    return '<img src="'.$imageSrc.'" width="80px" height="80px">';
                })
                ->editColumn('status', function (Program $program) {
                    return $program->status ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">In Active</span>';
                })
                ->addColumn('action', function (Program $program) use($user) {
                    $link = ($user->id == 1 || $user->hasPermissionTo('Edit Programs')) ?
                        '<a href="' . route('programs.edit', $program->id) . '" class="btn-purple btn mr-1" ><i class="fas fa-edit"></i></a>'
                        : "";
                    return $link;
                })
                ->rawColumns([ 'datetime', 'session_theme' ,'speaker_image', 'speaker_name', 'status', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.programs.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = User::find(auth()->user()->id);
        if( $user->id != 1 && !$user->hasPermissionTo('Create Programs')){
            abort(403);
        }
        return view('admin.programs.form')->with([
            'program' => new Program(),
            'program_speakers' => ProgramSpeaker::all(),
            'session_themes' => SessionTheme::where('status', 1)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required',
            'datetime' => 'required',
            'program_speaker_id' => 'required',
            'session_theme_id' => 'required',
            'status' => 'required',
        ]);
        $program = new Program();
        $program->topic = $request->topic;
        $program->datetime = date('Y-m-d H:i:s', strtotime($request->datetime));
        $program->program_speaker_id = $request->program_speaker_id;
        $program->session_theme_id = $request->session_theme_id;
        $program->status = $request->status;
        $program->save();
        return redirect()->route('programs.index')->with('success', 'Program created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('Edit Programs')){
            abort(403);
        }
        return view('admin.programs.form')->with([
            'program' => $program,
            'program_speakers' => ProgramSpeaker::all(),
            'session_themes' => SessionTheme::where('status', 1)->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'topic' => 'required',
            'datetime' => 'required',
            'program_speaker_id' => 'required',
            'session_theme_id' => 'required',
            'status' => 'required',
        ]);
        $program->topic = $request->topic;
        $program->datetime = date('Y-m-d H:i:s', strtotime($request->datetime));
        $program->program_speaker_id = $request->program_speaker_id;
        $program->session_theme_id = $request->session_theme_id;
        $program->status = $request->status;
        $program->save();
        return redirect()->route('programs.index')->with('success', 'Program updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        //
    }
}