<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Student;

class ConfirmstudentController extends Controller
{
    public function Student($id){
        $students = Student::with('course','teacher')->where([['course_id',$id],['status','confirmed']])->orderBy('id','DESC')
        ->paginate('10');
        $page = 'Coaching';
        return view('confirm_student.list',compact('students','page'));
    }
    public function Intership($id){
        $students = Student::with('course','teacher')->where([['course_id',$id],['status','intership']])->orderBy('id','DESC')
        ->paginate('10');
        $page = 'intern';
        return view('confirm_student.list',compact('students','page'));
    }
    public function GetCategory(){
        $courses = Course::select('id','name')->get();
        return response()->json($courses);
    }
}
