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
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Students</h3>
                <a href="{{ route('student.create') }}" class="btn btn-sm btn-primary">Add Student</a>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>
                    Students list
                </h6>
                <form id="FilterForm" action="" class="d-flex align-items-center ">
                    <input type="text" class='form-control m-1' name="filter" style="min-width: 150px;" id="filter"
                        placeholder="Student name">
                    <button class="btn btn-sm btn-primary form-control m-1" type="submit">Search</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>city</th>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse ($students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->gender ? 'Male' : 'Female' }}</td>
                                <td>{{ $student->city }}</td>
                                <td>{{ $student->course->name }}</td>
                                <td>{{ $student->teacher?->name ?? 'Unassigned' }}</td>
                                <td>{{ $student->phone }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->class ? 'Online' : 'Physical' }}</td>
                                <td>{{ $student->status }}</td>
                                <td>
                                    <a href="{{ route('student.edit', $student->id) }}" class="btn btn-sm btn-primary m-1">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger del-student m-1" data-id="{{ $student->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10"> No Student Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $students->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
@push('panel.js')
    <script>
        $(document).on('click', '.del-student', function() {
            var id = $(this).data('id');
            var btn = $(this)
            $.ajax({
                type: "DELETE",
                url: "{{ route('student.destroy', ':id') }}".replace(':id', id),
                data: "data",
                beforeSend: function() {
                    btn.prop('disabled', true);
                },
                success: function(data) {
                    btn.prop('disabled', false);
                    Swal.fire({
                        icon: 'success',
                        title: 'The Student deleted successfully',
                        text: `The ${data.name} Student deleted successfully`,
                    }).then(function() {
                        window.location.reload();
                    })
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
        $("#FilterForm").on('submit', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            console.log(formData)
            $.ajax({
                url: "{{ route('student.filter') }}",
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
                    $.each(data, function(index, student) {
                        $('#tbody').append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${student.name}</td>
                                <td>${student.gender ? 'Male' : 'Female'}</td>
                                <td>${student.city}</td>
                                <td>${student.course.name}</td>
                                <td>${student.teacher ? student.teacher.name : 'Unassigned'}</td>
                                <td>${student.phone}</td>
                                <td>${student.email}</td>
                                <td>${student.class ? 'Online' : 'Physical'}</td>
                                <td>${student.status}</td>
                                <td>
                                    <a href="/student/${student.id}/edit" class="btn btn-sm btn-primary m-1">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger del-student m-1" data-id="${student.id}">
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
    </script>
@endpush
