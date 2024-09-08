<?php

namespace App\Models;

use App\Models\Scopes\MemberActiveStatusFilterScope;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new MemberActiveStatusFilterScope);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        "phone",
        "user_number",
        "unit_name",
        "zone_name",
        "division_name",
        "dob",
        "gender",
        "status",
        "push_token",
        "age",
        "year_of_rukniyat"
    ];
    public function registration()
    {
        return $this->HasOne(Registration::class, 'member_id', 'id');
    }
    public function regFamilyDetails()
    {
        return $this->hasManyThrough(RegFamilyDetail::class, Registration::class);
    }
    public function scopeFilterByZone($query)
    {
        if(!empty(auth()->user()->zone_name))
             return $query->where('zone_name', auth()->user()->zone_name);
        return $query;
    }
    public function scopeFilterByRegionType($query, $type, $value, $zoneFilter = null) {
        if(!empty($value)) {
            return $query->where($type, $value);
        }
        if($type == 'unit_name') {
            if(!empty($zoneFilter)) {
                return $query->where('zone_name', $zoneFilter);
            }
        }
        return $query;
    }
}