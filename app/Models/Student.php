<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'father',
        'nic',
        'gender',
        'city',
        'address',
        'phone',
        'email',
        'course_id',
        'teacher_id',
        'class',
        'status'
    ];
    public function course(){
        return $this->belongsTo(Course::class,'course_id');
    }
    public function teacher(){
        return $this->belongsTo(Teacher::class,'teacher_id');
    }
}
