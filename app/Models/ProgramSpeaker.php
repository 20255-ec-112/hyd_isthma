<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class ProgramSpeaker extends Model
{
    use HasFactory, Mediable;

    protected $fillable = ['name', 'bio'];
}