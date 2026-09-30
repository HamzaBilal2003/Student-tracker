<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Expense;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        $total_students = Student::count();
        $total_pending_students = Student::where('status', 'pending')->count();
        $total_confirm_students = Student::where('status', 'confirmed')->count();
        $total_intern_students = Student::where('status', 'internship')->count();

        // Sum of course prices for each group
        $total_sum_total = Student::with('course')->get()->sum('course.price');
        $total_sum_pending = Student::where('status', 'pending')->with('course')->get()->sum('course.price');
        $total_sum_confirmed = Student::where('status', 'confirmed')->with('course')->get()->sum('course.price');
        $total_sum_internship = Student::where('status', 'internship')->with('course')->get()->sum('course.price');

        $currentYear = Carbon::now()->year;

        $total_year_expense = Expense::whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.index', compact(
            'total_students',
            'total_pending_students',
            'total_confirm_students',
            'total_intern_students',
            'total_sum_total',
            'total_sum_pending',
            'total_sum_confirmed',
            'total_sum_internship',
            'total_year_expense'
        ));

    }
}
