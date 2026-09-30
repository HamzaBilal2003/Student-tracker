@extends('layout.layout')
@section('panel')
    <div class="card my-2">
        <div class="card-header">
            <h3 class="card-title">Students</h3>
        </div>
        <div class="card-body">
            <form id="studentForm" action="" method="POST">
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" value="{{$student->name}}" class="form-control" placeholder="Enter name">
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="father">Father's Name</label>
                        <input type="text" name="father" value="{{$student->father}}" id="father" class="form-control"
                            placeholder="Enter father's name">
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="nic">NIC</label>
                        <input type="text" name="nic" id="nic" value='{{$student->nic}}' class="form-control" placeholder="Enter NIC">
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="1" {{$student->gender ? 'selected' : ''}}>Male</option>
                            <option value="0" {{!$student->gender ? 'selected' : ''}}>Female</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="city">City</label>
                        <input type="text" name="city" value="{{$student->city}}" id="city" class="form-control" placeholder="Enter city">
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" value='{{$student->address}}' class="form-control"
                            placeholder="Enter address">
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" value='{{$student->phone}}' class="form-control"
                            placeholder="Enter phone number">
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value='{{$student->email}}' class="form-control" placeholder="Enter email">
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="course_id">Course</label>
                        <select name="course_id" id="course_id" class="form-control">
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{($course->id == $student->course_id ) ? 'selected' : ''}} >{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="teacher_id">Teacher</label>
                        <select name="teacher_id" id="teacher_id" class="form-control">
                            <option value="">Select teacher (optional)</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{($teacher->id == $student->teacher_id ) ? 'selected' : ''}}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="class">Class Type</label>
                        <select name="class" id="class" class="form-control">
                            <option value="1" {{$student->class ? 'selected' : ''}}>Online</option>
                            <option value="0" {{!$student->class ? 'selected' : ''}}>Physical</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label for="status">Status</label>
                        <select name="Sstatus" id="Sstatus" class="form-control">
                            <option value="pending" {{($student->status == 'pending') ? 'selected' : ''}}>Pending</option>
                            <option value="confirmed" {{($student->status == 'confirmed') ? 'selected' : ''}}>Confirmed</option>
                            @if ($student->status == 'confirmed' || $student->status == 'internship')
                            <option value="internship" {{($student->status == 'internship') ? 'selected' : ''}}>Internship</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn btn-primary w-100">Submit</button>
                    </div>
                </div>
            </form>       
        </div>
    </div>
@endsection
@push('panel.js')
<script>
    $(document).ready(function() {

        $('#duration').val('{{$course->duration}}')
        $('input , textarea').on('change',function(){
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        })
        $('#studentForm').on('submit', function(e) {
            e.preventDefault();
    
            let formData = $(this).serialize();
            console.log(formData);
            // AJAX request
            $.ajax({
                url: "{{ route('student.update',':id') }}".replace(':id',{{$student->id}}),
                type: "PUT",
                data: formData,
                beforeSend: function(){
                    $('#studentForm').find('button[type="submit"]').attr('disabled', true);
                },
                success: function(response) {
                    // Handle success response
                    $('#studentForm').find('button[type="submit"]').attr('disabled', false);
                    Swal.fire({
                        icon : 'success',
                        title : 'success',
                        text : response.message || "The student is updated successfully"
                    }).then(function(){
                        window.location.href = '{{route('student.index')}}';
                    });
                    $('#studentForm')[0].reset(); // Reset the form
                },
                error: function(xhr, status, error) {
                    $('#studentForm').find('button[type="submit"]').attr('disabled', false);
                    // Handle error response
                    console.log(xhr)
                    Swal.fire({
                        icon : 'error',
                        title : 'Something went wrong',
                        text : response.message || "Try again"
                    });
                    let errors = xhr.responseJSON.errors; 
                    console.log(errors);
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