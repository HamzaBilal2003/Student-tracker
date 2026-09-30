@extends('layout.layout')
@push('panel.css')
    <style>
        .td-desc {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-can {
            min-width: 150px;
        }
    </style>
@endpush
@section('panel')
    <div class="card my-2">
        <div class="card-header">
            <div class="">
                <h3 class="card-title">Expenses</h3>
                <div>
                    <form action="" id="categoryForm" class="row">
                        <input type="hidden" name="expense_id" id="expense_id">
                        <div class="col-md-6 mt-2">
                            <label for="expense">Expense</label>
                            <input type="text" name="expense" id="expense" class="form-control">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="status">Amount</label>
                            <input type="number" name='amount' id="amount" class="form-control">
                        </div>
                        <div class="col-md-12 mt-2">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" placeholder="Enter description e.g ( The repairement of table or chair )"></textarea>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button class="btn btn-primary w-100" id='submit-btn' type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>
                    Expense list
                </h6>
                <form id="FilterForm" action="" class="d-flex align-items-center ">
                    <input type="text" class='form-control m-1' name="filter" style="min-width: 150px;" id="filter" placeholder="Expense">
                    <button class="btn btn-sm btn-primary form-control m-1" type="submit">Search</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Expense</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $expense->expense }}</td>
                                <td>Rs {{ $expense->amount }}</td>
                                <td>
                                    {{$expense->description}}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-category" data-expense="{{$expense}}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="del-category btn btn-sm btn-danger" data-id="{{$expense->id}}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    No Expense found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{$expenses->links('pagination::bootstrap-4')}}
        </div>
    </div>
@endsection
@push('panel.js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-category', function() {
                var data = $(this).data('expense');
                $('#expense').val(data.expense);
                $('#amount').val(data.amount);
                $('#description').val(data.description);
                $('#expense_id').val(data.id);
                $('#submit-btn').text('Update');
                console.log(data ,' edit btn clicked!!!  ');
            });
            $(document).on('click', '.del-category', function() {
                var id = $(this).data('id');
                var btn = $(this)
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('expense.destroy', ':id') }}".replace(':id', id),
                    data: "data",
                    beforeSend: function() {
                        btn.prop('disabled', true);
                    },
                    success: function(data) {
                        btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'The Expense deleted successfully',
                            text: `The ${data.name} Expense deleted successfully`,
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error deleting course',
                        })
                    }
                });
            });
            $('input , select').on('change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            })
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let id = $('#expense_id').val();
                let url = (id != '') ? '{{route('expense.update',":id")}}'.replace(':id',id) : '{{route('expense.store')}}';
                let method = (id != '') ? 'PUT' : 'POST';
                // AJAX request
                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    beforeSend:function(){
                        $('#categoryForm').find('button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        $('#categoryForm').find('button[type="submit"]').prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'success',
                            text: response.message || "The expense is add successfully"
                        }).then(function() {
                            window.location.reload();
                        });
                        $('#courseForm')[0].reset(); // Reset the form
                    },
                    error: function(xhr, status, error) {
                        $('#categoryForm').find('button[type="submit"]').prop('disabled', false);
                        // Handle error response
                        console.log(xhr)
                        let errors = xhr.responseJSON.errors;
                        console.log(errors);
                        Object.keys(errors).forEach(function(key) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}`).after(
                                `<h6 class='invalid-feedback d-block'>${errors[key][0]}</h6>`
                                )
                        });
                    }
                });
            });

            $("#FilterForm").on('submit',function(event){
                event.preventDefault();
                var formData = $(this).serialize();
                console.log(formData)
               $.ajax({
                url: "{{route('expense.filter')}}",
                type: "POST",   
                data: formData,
                beforeSend:function(){
                    $('#FilterForm').find('button[type="submit"]').prop('disabled', true);
                    $('#tbody').empty();
                    $('#tbody').append(`<tr><td colspan='4'>Loading...</td></tr>`);
                },
                success : function(data){
                    $('#FilterForm').find('button[type="submit"]').prop('disabled', false);
                    console.log(data);
                    $('#tbody').empty();
                    $.each(data, function (index, expense) { 
                        $('#tbody').append(`
                            <tr>
                                <td>${ index + 1 }</td>
                                <td>${ expense.expense }</td>
                                <td>Rs ${ expense.amount }</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-category" data-expense='${JSON.stringify(expense)}'>
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="del-category btn btn-sm btn-danger" data-id='${expense.id}'>
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                },
                error : function(xhr, status, error) {
                    $('#FilterForm').find('button[type="submit"]').prop('disabled', false);
                    console.log(xhr);
                    console.log(error);
                }
               })
            })
        });
    </script>
@endpush
