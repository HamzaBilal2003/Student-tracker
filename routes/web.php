<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfirmstudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AuthMiddleware;

Route::get('/', function () {
    if (Auth::check()){
        return redirect()->route('dashboard.index');
    }
    return view('login');
})->name('auth.loginForm');
Route::post('login/check',[UserController::class,'login'])->name('auth.login');

Route::middleware(AuthMiddleware::class)->group(function () {

    Route::get('logout/check',[UserController::class,'logout'])->name('auth.logout');
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');
    Route::resource('courses',CourseController::class);
    Route::resource('category',CategoryController::class);
    Route::resource('teacher',TeacherController::class);
    Route::resource('student',StudentController::class);
    Route::get('get-category',[ConfirmstudentController::class,'GetCategory'])->name('student.category');
    Route::get('confirm-student/{id}',[ConfirmstudentController::class,'Student'])->name('student.conformed');
    Route::get('intership-student/{id}',[ConfirmstudentController::class,'Intership'])->name('student.Intership');
    Route::resource('expense',ExpenseController::class);
    Route::resource('payment',PaymentController::class);
    
    
    // filters 
    
       //category namefilter
    Route::post('category/filter',[CategoryController::class,'filter'])->name('category.filter');
    Route::post('teacher/filter',[TeacherController::class,'filter'])->name('teacher.filter');
    Route::post('course/filter',[CourseController::class,'filter'])->name('course.filter');
    Route::post('student/filter',[StudentController::class,'filter'])->name('student.filter');
    Route::post('expense/filter',[ExpenseController::class,'filter'])->name('expense.filter');
});

