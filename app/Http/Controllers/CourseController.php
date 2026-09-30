<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with('category')->orderBy('id','DESC')->paginate('10');
        return view('courses.list',compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();
        return view('courses.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|string',
            'languages' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id'
        ]);

        Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'languages' => $request->languages,
            'category_id' => $request->category_id
        ]);
    
        return response()->json(['message' => 'Course added successfully!'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $categories = Category::get();
        return view('courses.edit',compact('course','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|string',
            'languages' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id'
        ]);

        $course->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'languages' => $request->languages,
            'category_id' => $request->category_id
        ]);
    
        return response()->json(['message' => 'Course added successfully!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $name = $course->name;
        $course->delete();
        return response()->json([
            'name' => $name
        ]);
    }
    public function filter(Request $request){
        $coursers = Course::with('category')->where('name', 'like', '%' . $request->filter .'%')->get();
        return response()->json($coursers);
    }
}
