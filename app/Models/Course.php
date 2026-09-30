<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'languages',
        'category_id'
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }    
}
