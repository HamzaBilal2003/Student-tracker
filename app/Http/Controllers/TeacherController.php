<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::with('category')->orderBy('id','DESC')->paginate('10');
        $categories = Category::orderBy('id','DESC')->get();
        return view('teacher.list',compact('teachers','categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'category_id' => 'required|integer|exists:categories,id',
            'exp'=>'required|string',
            'Tstatus' => 'required|in:1,0',
            'salary' => 'required|numeric'
        ]);
        Teacher::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'category_id' => $request->category_id,
            'exp' => $request->exp,
            'status' => $request->Tstatus,
            'salary' => $request->salary
        ]);
        return response()->json([
            'message' => 'Teacher created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'category_id' => 'required|integer|exists:categories,id',
            'exp'=>'required|string',
            'Tstatus' => 'required|in:1,0',
            'salary' => 'required|numeric'
        ]);
        $teacher->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'category_id' => $request->category_id,
            'exp' => $request->exp,
            'status' => $request->Tstatus,
            'salary' => $request->salary
        ]);
        return response()->json([
            'message' => 'Teacher updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        $name = $teacher->name;
        $teacher->delete();
        return response()->json([
            'name' => $name,
            "message" => 'The Teacher data deleted successfully'
        ]);
    }
    public function filter(Request $request){
        $teachers = Teacher::with('category')->where('name', 'like', '%' . $request->filter .'%')->get();
        // return $teachers;
        return response()->json($teachers);
    }
}
