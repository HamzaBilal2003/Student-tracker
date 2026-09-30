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
                <h3 class="card-title">Teachers</h3>
                <div>
                    <form action="" id="teacherForm" class="row">
                        <input type="hidden" name="teacher_id" id="teacher_id">
                        <div class="col-md-6 mt-2">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Enter name"
                                class="form-control">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="exp">Experience</label>
                            <input type="text" name="exp" id="exp"
                                placeholder="Enter experience e.g ( 2 year )" class="form-control">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" placeholder="Enter phone no"
                                class="form-control">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Enter email"
                                class="form-control">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="salary">Salary</label>
                            <input type="number" name="salary" id="salary" class="form-control" placeholder="Enter Salary e.g (Rs 150000)">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="status">Status</label>
                            <select name="Tstatus" id="Tstatus" class="form-control d-block">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button class="btn btn-primary w-100" id="submit-btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>
                    Teacher list
                </h6>
                <form id="FilterForm" action="" class="d-flex align-items-center ">
                    <input type="text" class='form-control m-1' name="filter" style="min-width: 150px;" id="filter"
                        placeholder="Teacher name">
                    <button class="btn btn-sm btn-primary form-control m-1" type="submit">Search</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Experience</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->category->name }}</td>
                                <td>{{ $teacher->exp }}</td>
                                <td>{{ $teacher->phone }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>
                                    {{$teacher->salary}}
                                </td>
                                <td>
                                    <span class="btn btn-sm btn-{{ $teacher->status ? 'success' : 'danger' }}"></span>
                                </td>
                                <td class="btn-can">
                                    <button class="btn btn-sm btn-primary edit-teacher m-2"
                                        data-teacher="{{ $teacher }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger del-teacher m-2" data-id="{{ $teacher->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No teacher found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $teachers->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
@push('panel.js')
    <script>
        $(document).ready(function() {
            // Edit teacher
            $(document).on('click', '.edit-teacher', function() {
                var data = $(this).data('teacher');
                $('#name').val(data.name);
                $('#exp').val(data.exp);
                $('#phone').val(data.phone);
                $('#email').val(data.email);
                $('#category_id').val(data.category_id);
                $('#status').val(data.status);
                $('#teacher_id').val(data.id);
                $('#salary').val(data.salary);
                $('#submit-btn').text('Update');
                console.log(data);
            });

            // Delete teacher
            $(document).on('click', '.del-teacher', function() {
                var id = $(this).data('id');
                var btn = $(this);
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('teacher.destroy', ':id') }}".replace(':id', id),
                    beforeSend: function() {
                        btn.prop('disabled', true);
                    },
                    success: function(data) {
                        btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `The ${data.name} teacher data was deleted successfully`,
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error deleting teacher',
                        });
                    }
                });
            });

            // Clear validation errors
            $('input, select').on('change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });

            // Submit form
            $('#teacherForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let id = $('#teacher_id').val();
                let url = (id != '') ? '{{ route('teacher.update', ':id') }}'.replace(':id', id) :
                    '{{ route('teacher.store') }}';
                let method = (id != '') ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    beforeSend: function() {
                        $('#teacherForm').find('button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        $('#teacherForm').find('button[type="submit"]').prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message ||
                                "Teacher information saved successfully",
                        }).then(function() {
                            window.location.reload();
                        });
                        $('#teacherForm')[0].reset(); // Reset the form
                    },
                    error: function(xhr) {
                        $('#teacherForm').find('button[type="submit"]').prop('disabled', false);
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}`).after(
                                `<h6 class='invalid-feedback d-block'>${errors[key][0]}</h6>`
                            );
                        });
                    }
                });
            });
            $("#FilterForm").on('submit', function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('teacher.filter') }}",
                    type: "POST",
                    data: formData,
                    beforeSend: function() {
                        $('#FilterForm').find('button[type="submit"]').prop('disabled', true);
                        $('#tbody').empty();
                        $('#tbody').append(`<tr><td colspan='4'>Loading...</td></tr>`);
                    },
                    success: function(data) {
                        $('#FilterForm').find('button[type="submit"]').prop('disabled', false);
                        console.log(data);
                        $('#tbody').empty();
                        console.log(data)
                        $.each(data, function(index, teacher) {
                            $('#tbody').append(`
                                    <tr>
                                        <td>${ index  + 1}</td>
                                        <td>${ teacher.name }</td>
                                        <td>${teacher.category.name }</td>
                                        <td>${ teacher.exp }</td>
                                        <td>${teacher.phone }</td>
                                        <td>${ teacher.email }</td>
                                        <td>
                                            <span class="btn btn-sm btn-${ teacher.status ? 'success' : 'danger' }"></span>
                                        </td>
                                        <td class="btn-can">
                                            <button class="btn btn-sm btn-primary edit-teacher m-2"
                                                data-teacher='${JSON.stringify(teacher)}'>
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger del-teacher m-2" data-id="${teacher.id}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    `);
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#FilterForm').find('button[type="submit"]').prop('disabled', false);
                        console.log(xhr);
                        console.log(error);
                    }
                })
            })
        });
    </script>
@endpush
