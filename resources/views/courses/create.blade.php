@extends('layout.layout')
@section('panel')
    <div class="card my-2">
        <div class="card-header">
            <h3 class="card-title">Courses</h3>
        </div>
        <div class="card-body">
            <form action="" method="POST" id="courseForm">
                <div class="form-group">
                    <label for="name">Course Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter course name" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="languages">Languages</label>
                    <input type="text" class="form-control" id="languages" name="languages" placeholder="Enter languages (e.g., 'Html, Css')" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter course description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Enter course price" required>
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <select name="duration" id="duration" class="form-control" required>
                        <option value="">Select Duration e.g (month)</option>
                        <option value="1 month">1 month</option>
                        <option value="2 month">2 month</option>
                        <option value="3 month">3 month</option>
                        <option value="4 month">4 month</option>
                        <option value="5 month">5 month</option>
                        <option value="6 month">6 month</option>
                    </select>
                </div>
                
                
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>            
        </div>
    </div>
@endsection
@push('panel.js')
<script>
    $(document).ready(function() {
        $('input , textarea').on('change',function(){
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        })
        $('#courseForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
    
            // Collect form data
            let formData = $(this).serialize();

    
            // AJAX request
            $.ajax({
                url: "{{ route('courses.store') }}",
                type: "POST",
                data: formData,
                beforeSend:function(){
                    $('#courseForm').find('button[type="submit"]').attr('disabled', true);
                },
                success: function(response) {
                    $('#courseForm').find('button[type="submit"]').attr('disabled', false);
                    // Handle success response
                    Swal.fire({
                        icon : 'success',
                        title : 'success',
                        text : response.message || "The course is add successfully"
                    }).then(function(){
                        window.location.href = '{{route('courses.index')}}';
                    });
                    $('#courseForm')[0].reset(); // Reset the form
                },
                error: function(xhr, status, error) {
                    $('#courseForm').find('button[type="submit"]').attr('disabled', false);
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key){
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}`).after(`<h6 class='invalid-feedback d-block'>${errors[key][0]}</h6>`)
                    });
                    // Object.keys(errors).forEach(function(key) {
                    //             $(`#${key}`).closest('#taskForm').find('.invalid-feedback')
                    //                 .text(errors[key][0]); // Display validation errors
                    //         });
                }
            });
        });
    });
    </script>
@endpush