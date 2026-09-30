<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
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
            'student_id',
            'amount'
        ]);
        Payment::create($request->all());
        return response()->json([
            'message' => 'Payment created successfully'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::with(['course' => function($q){
            $q->select(['id', 'name','price']);
        }])->select(['id','name','course_id'])->find($id);
        $payments = Payment::where('student_id',$student->id)->orderBy('id','DESC')->paginate('10');
        return view('confirm_student.payment', compact('student','payments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'student_id',
            'amount'
        ]);
        $payment->update([
            'amount' => $request->amount
        ]);
        return response()->json([
            'message' => 'Payment update successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }
}
