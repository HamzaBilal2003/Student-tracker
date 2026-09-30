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
                <h3 class="card-title">Courses</h3>
                <a href="{{ route('courses.create') }}" class="btn btn-sm btn-primary">Add Courses</a>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>
                    Courses list
                </h6>
                <form id="FilterForm" action="" class="d-flex align-items-center ">
                    <input type="text" class='form-control m-1' name="filter" style="min-width: 150px;" id="filter"
                        placeholder="Course name">
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
                            <th>Languages</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse ($courses as $course)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->category->name }}</td>
                                <td>{{ $course->languages }}</td>
                                <td>{{ $course->price }}</td>
                                <td>{{ $course->duration }}</td>
                                <td class="td-desc">{{ $course->description }}</td>
                                <td class="btn-can">
                                    <a class="btn btn-sm btn-primary m-2" href="{{ route('courses.edit', $course->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger del-course m-2" data-id='{{ $course->id }}'>
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    No course found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $courses->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
@push('panel.js')
    <script>
        $(document).on('click', '.del-course', function() {
            var id = $(this).data('id');
            var btn = $(this)
            $.ajax({
                type: "DELETE",
                url: "{{ route('courses.destroy', ':id') }}".replace(':id', id),
                data: "data",
                beforeSend: function() {
                    btn.prop('disabled', true);
                },
                success: function(data) {
                    btn.prop('disabled', false);
                    Swal.fire({
                        icon: 'success',
                        title: 'The course deleted successfully',
                        text: `The ${data.name} course deleted successfully`,
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
        })
        $("#FilterForm").on('submit', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            console.log(formData)
            $.ajax({
                url: "{{ route('course.filter') }}",
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
                    $.each(data, function(index, course) {
                        let url = '{{route('courses.edit',":id")}}'.replace(':id',course.id);
                        $('#tbody').append(`
                                <tr>
                                    <td>${ index + 1 }</td>
                                    <td>${ course.name }</td>
                                    <td>${ course.category.name}</td>
                                    <td>${ course.languages }</td>
                                    <td>${ course.price }</td>
                                    <td>${ course.duration }</td>
                                    <td class="td-desc">{{ $course->description }}</td>
                                    <td class="btn-can">
                                        <a class="btn btn-sm btn-primary m-2" href="${url}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger del-course m-2" data-id='${ course.id }'>
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
