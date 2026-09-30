@extends('layout.layout')
@section('panel')
    <div class="card my-2">
        <div class="card-header">
            <h5 class="card-title">Dashboard</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 p-2">

                    <div class="card bg-primary text-light p-4">
                        <h5>Total <br> Student</h5>
                        <h6>{{ $total_students }}</h6>
                        <h6>Net Worth:- Rs {{ $total_sum_total }}</h6>
                    </div>
                </div>
                <div class="col-md-3 p-2">
                    <div class="card bg-primary text-light p-4">
                        <h5>Total Pending Student</h5>
                        <h6>{{ $total_pending_students }}</h6>
                        <h6>Net Worth:- Rs {{ $total_sum_pending }}</h6>
                    </div>
                </div>
                <div class="col-md-3 p-2">
                    <div class="card bg-primary text-light p-4">
                        <h5>Total confirmed Student</h5>
                        <h6>{{ $total_confirm_students }}</h6>
                        <h6>Net Worth:- Rs {{ $total_sum_confirmed }}</h6>
                    </div>
                </div>
                <div class="col-md-3 p-2">
                    <div class="card bg-primary text-light p-4">
                        <h5>Total Intern <br> Student</h5>
                        <h6>{{ $total_intern_students }}</h6>
                        <h6>Net Worth:- Rs {{ $total_sum_internship }}</h6>
                    </div>
                </div>

            </div>
            <div class="card p-1 bg-primary text-light">
                <div class="card-header">
                    <div class="card-title">
                        <h5>Expenses</h5>
                    </div>
                </div>
                <div class="table-responsive rounded text-dark bg-light">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($total_year_expense as $expense)
                                <tr>
                                    <td>{{ DateTime::createFromFormat('!m', $expense->month)->format('F') }}</td>
                                    <td>Rs {{ $expense->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('panel.js')
    <script>
        console.log(@json($total_year_expense))
    </script>
@endpush
