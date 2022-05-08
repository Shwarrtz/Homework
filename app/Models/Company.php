<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'reg_num',
        'found_date',
        'country_id',
        'zip_code',
        'city_id',
        'street_address',
        'latitude',
        'longitude',
        'owner',
        'employees',
        'activity_id',
        'active',
        'email',
        'password'
    ];
    
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }
}
