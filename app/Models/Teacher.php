<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable =[
        'name',
        'exp',
        'phone',
        'email',
        'category_id',
        'status',
        'salary'
    ];
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }
}
