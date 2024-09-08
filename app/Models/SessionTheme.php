<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTheme extends Model
{
    use HasFactory;
    protected $fillable = [
        'theme_name',
        'status',
        'theme_type'
    ];
}