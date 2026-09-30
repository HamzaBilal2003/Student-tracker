<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('id','DESC')->paginate('10');
        return view('categories.list',compact('categories'));
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
            'category' => 'required|string',
            'Cstatus' => 'required|in:1,0'
        ]);
        Category::create([
            'name' => $request->category,
            'status' => $request->Cstatus,
        ]);
        return response()->json([
            'message'=> 'The category created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $name)
    {   
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category' => 'required|string',
            'Cstatus' => 'required|in:1,0'
        ]);
        $category->update([
            'name' => $request->category,
            'status' => $request->Cstatus,
        ]);
        return response()->json([
            'message'=> 'The category edit successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();
        return response()->json([
            'name' => $name,
            'message' => 'The category deleted successfully',
        ]);
    }
    public function filter(Request $request){
        $categories = Category::where('name', 'like', '%' . $request->filter .'%')->get();
        return response()->json($categories);
    }
}
