<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::orderBy('id','DESC')->paginate('10');
        return view('expense.list', compact('expenses'));
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
            'expense' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'required|string'
        ]);
        Expense::create($request->all());
        return response()->json([
            'message' => 'Expense created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'required|string'
        ]);
        $expense->update($request->all());
        return response()->json([
            'message' => 'Expense updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $name = $expense->expense;
        $expense->delete();
        return response()->json([
            'name' => $name,
            'message' => 'Expense deleted successfully'
        ]);
    }
    public function filter(Request $request){
        $categories = Expense::where('expense', 'like', '%' . $request->filter .'%')->get();
        return response()->json($categories);
    }
}
