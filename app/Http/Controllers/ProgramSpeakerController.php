<?php

namespace App\Http\Controllers;

use App\Models\ProgramSpeaker;
use App\Models\User;
use Illuminate\Http\Request;
use Plank\Mediable\Facades\MediaUploader;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class ProgramSpeakerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataTables $datatable)
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View ProgramSpeakers')){
            abort(403);
        }
        if($request->ajax()) {
            $query = ProgramSpeaker::query();
            return $datatable->eloquent($query)
                ->addColumn('speaker_image', function (ProgramSpeaker $speaker) {
                    $imageSrc = !empty($speaker->getMedia('speaker_image')->first()) ? $speaker->getMedia('speaker_image')->first()->getUrl() : '/assets/img/no-image.png';
                    return '<img src="'.$imageSrc.'" width="80px" height="80px">';
                })
                ->addColumn('action', function (ProgramSpeaker $speaker) use($user) {
                    $link = ($user->id == 1 || $user->hasPermissionTo('Edit ProgramSpeakers')) ?
                        '<a href="' . route('programSpeakers.edit', $speaker->id) . '" class="btn-purple btn mr-1" ><i class="fas fa-edit"></i></a>'
                        : "";
                    return $link;
                })
                ->rawColumns([ 'speaker_image', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.programspeakers.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('Create ProgramSpeakers')){
            abort(403);
        }
        return view('admin.programspeakers.form')->with([
            'speaker' => new ProgramSpeaker(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'bio' => 'required'
        ]);
        $speaker = new ProgramSpeaker();
        $speaker->name = $request->name;
        $speaker->bio = $request->bio;
        $speaker->save();

        if(!empty($request->file('speaker_image'))) {
            $media = MediaUploader::fromSource($request->file('speaker_image'))->toDestination('public', 'images/speaker_image')->useFilename(Str::uuid())->upload();
            $speaker->attachMedia($media, ['speaker_image']);
        }
        return redirect()->route('programSpeakers.index')->with('success', 'Speaker created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramSpeaker $programSpeaker)
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
        return view('admin.programspeakers.form')->with([
           'speaker' => ProgramSpeaker::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        $speaker = ProgramSpeaker::find($id);
        $data = $request->validate([
            'name' => 'required',
            'bio' => 'required',
        ]);
        $speaker->update($data);
        if(!empty($request->file('speaker_image'))) {
            $uploadedImages = $speaker->getMedia('speaker_image')->first();
            if(!empty($uploadedImages)) {
                $speaker->detachMedia($speaker->id);
                $uploadedImages->delete();
            }
            $media = MediaUploader::fromSource($request->file('speaker_image'))->toDestination('public', 'images/speaker_image')->useFilename(Str::uuid())->upload();
            $speaker->attachMedia($media, ['speaker_image']);
        }
        return redirect()->route('programSpeakers.index')->with('success', 'Speaker updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramSpeaker $programSpeaker)
    {
        //
    }
}