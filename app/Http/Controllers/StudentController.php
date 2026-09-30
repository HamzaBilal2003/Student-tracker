<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Category;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('course','teacher')->where('status','pending')->orderBy('id','DESC')
                    ->paginate('10');
        return view('student.list',compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::orderBy('id','DESC')->get();
        $teachers = Teacher::orderBy('id','DESC')->get();
        return view('student.create',compact('courses','teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'father' => 'required|string|max:255',
            'nic' => 'required|string|max:20',
            'gender' => 'required|boolean',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|email|max:255',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'class' => 'required|boolean',
            'Sstatus' => 'required|string|in:pending,confirmed'
        ]);

        // Create a new student record
        $student = Student::create([
            'name' => $request->name,
            'father' => $request->father,
            'nic' => $request->nic,
            'gender' => $request->gender,
            'city' => $request->city,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'course_id' => $request->course_id,
            'teacher_id' => $request->teacher_id,
            'class' => $request->class,
            'status' => $request->Sstatus,
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Student added successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $courses = Course::orderBy('id','DESC')->get();
        $teachers = Teacher::orderBy('id','DESC')->get();
        return view('student.edit',compact('student','courses','teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'father' => 'required|string|max:255',
            'nic' => 'required|string|max:20',
            'gender' => 'required|boolean',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|email|max:255',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'class' => 'required|boolean',
            'Sstatus' => 'required|string|in:pending,confirmed,intership'
        ]);

        // Create a new student record
        $student->update([
            'name' => $request->name,
            'father' => $request->father,
            'nic' => $request->nic,
            'gender' => $request->gender,
            'city' => $request->city,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'course_id' => $request->course_id,
            'teacher_id' => $request->teacher_id,
            'class' => $request->class,
            'status' => $request->Sstatus,
        ]);

        // Return a success response
        return response()->json([
            
            'message' => 'Student updated successfully!'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $name = $student->name;
        $student->delete();
        return response()->json([
            'name'=> $name,
            'message' => 'Student deleted successfully!'
        ]);
    }
    public function filter(Request $request){
        $categories = Student::with('course','teacher')->where('name', 'like', '%' . $request->filter .'%')->get();
        return response()->json($categories);
    }

}
