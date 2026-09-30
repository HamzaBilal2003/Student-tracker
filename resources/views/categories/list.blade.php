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
                <h3 class="card-title">Categories</h3>
                <div>
                    <form action="" id="categoryForm" class="row">
                        <input type="hidden" name="category_id" id="category_id">
                        <div class="col-md-6 mt-2">
                            <label for="category">Category</label>
                            <input type="text" name="category" id="category" class="form-control">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="status">Status</label>
                            <select name="Cstatus" id="Cstatus" class="form-control d-block">
                                <option value="1">Active</option>
                                <option value="0">Unactive</option>
                            </select>
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
                    Categories list
                </h6>
                <form id="FilterForm" action="" class="d-flex align-items-center ">
                    <input type="text" class='form-control m-1' name="filter" style="min-width: 150px;" id="filter" placeholder="Category">
                    <button class="btn btn-sm btn-primary form-control m-1" type="submit">Search</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{$category->name}}</td>
                                <td>
                                    <span class="btn btn-sm btn-{{$category->status ? 'success' : 'danger'}}"></span>
                                </td>
                                <td class="btn-can">
                                    <button class="btn btn-sm btn-primary edit-category m-2" data-category="{{$category}}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger del-category m-2" data-id='{{$category->id}}'> 
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    No category found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{$categories->links('pagination::bootstrap-4')}}
        </div>
    </div>
@endsection
@push('panel.js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-category', function() {
                var data = $(this).data('category');
                $('#category').val(data.name);
                $('#Cstatus').val(data.status);
                $('#category_id').val(data.id);
                $('#submit-btn').text('Update');
                console.log(data ,' edit btn clicked!!!  ');
            });
            $(document).on('click', '.del-category', function() {
                var id = $(this).data('id');
                var btn = $(this)
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('category.destroy', ':id') }}".replace(':id', id),
                    data: "data",
                    beforeSend: function() {
                        btn.prop('disabled', true);
                    },
                    success: function(data) {
                        btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'The Category deleted successfully',
                            text: `The ${data.name} category deleted successfully`,
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
                let id = $('#category_id').val();
                let url = (id != '') ? '{{route('category.update',":id")}}'.replace(':id',id) : '{{route('category.store')}}';
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
                            text: response.message || "The course is add successfully"
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
                url: "{{route('category.filter')}}",
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
                    $.each(data, function (index, category) { 
                        $('#tbody').append(`
                            <tr>
                                <td>${ index + 1}</td>
                                <td>${ category.name}</td>
                                <td>
                                    <span class="btn btn-sm btn-${category.status ? 'success' : 'danger'}"></span>
                                </td>
                                <td class="btn-can">
                                    <button class="btn btn-sm btn-primary edit-category m-2" data-category='${JSON.stringify(category)}'>
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger del-category m-2" data-id='${category.id}'> 
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
