<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'staff_id',
        'title',
        'first_name',
        'other_name',
        'surname',
        'gender',
        'phone',
        'picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cordinator_modules()
    {
        return $this->hasMany(Module::class, 'cordinator_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'lecturer_module',  'lecturer_id', 'module_id');
    }

    public function full_name()
    {
        return $this->other_name ? $this->title . ' ' . $this->first_name . ' ' . $this->other_name . ' ' . $this->surname : $this->first_name . ' ' . $this->surname;
    }

    public function picture_url()
    {
        return $this->picture ? asset('assets/img/lecturers/' . $this->picture) : asset('assets/img/lecturers/default.png');
    }

    // public function emergencycontact()
    // {
    //     return $this->hasMany(EmergencyContact::class);
    // }

}