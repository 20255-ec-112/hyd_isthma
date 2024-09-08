<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramListResource;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    public function listPrograms() {
        $programs = Program::with('sessionTheme', 'programSpeaker')->get();
        return response()->json([
            "data" => ProgramListResource::collection($programs)
        ]);
    }
}