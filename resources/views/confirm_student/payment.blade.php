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
                <h3 class="card-title">Payment</h3>
                <h6>{{ $student->name }}</h6>
                <h6><span class="text-primary">Total Amount:-</span> Rs {{ $student->course->price }}</h6>
                <h6><span class="text-primary">Payed Amount:-</span> Rs {{ $payments->sum('amount') }}</h6>
                <h6><span class="text-primary">Pending Amount:-</span> Rs {{ $student->course->price - $payments->sum('amount') }}</h6>
                <div>
                    <form action="" id="categoryForm" class="row">
                        <input type="hidden" name="payment_id" id="payment_id">
                        <input type="hidden" name="student_id" id="student_id" value='{{$student->id}}'>
                        <div class="col-md-12 mt-2">
                            <label for="status">Amount</label>
                            <input type="number" name='amount' max="{{ $student->course->price - $payments->sum('amount') }}" id="amount" class="form-control">
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
                    Payment list
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-payment" data-payment="{{$payment}}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger del-payment" data-id="{{$payment->id}}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    No payment found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{$payments->links('pagination::bootstrap-4')}}
        </div>
    </div>
@endsection
@push('panel.js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-payment', function() {
                var data = $(this).data('payment');
                $('#payment_id').val(data.id);
                $('#amount').val(data.amount);
                $('#submit-btn').text('Update');
                console.log(data ,' edit btn clicked!!!  ');
            });
            $(document).on('click', '.del-payment', function() {
                var id = $(this).data('id');
                var btn = $(this)
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('payment.destroy', ':id') }}".replace(':id', id),
                    beforeSend: function() {
                        btn.prop('disabled', true);
                    },
                    success: function(data) {
                        btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment',
                            text: `The  Payment deleted successfully`,
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
                let id = $('#payment_id').val();
                let url = (id != '') ? '{{route('payment.update',":id")}}'.replace(':id',id) : '{{route('payment.store')}}';
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
                            text: response.message || "The Payment is add successfully"
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

        });
    </script>
@endpush
