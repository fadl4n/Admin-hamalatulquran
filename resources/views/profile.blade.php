@extends('admin_template')

@section('title page')
    Profile
@endsection
@section('content')
    <style>
        .form-control-feedback {
        pointer-events: all;
        }
    </style>
    <div class="mt-3">
        <div class="card">
            <div class="card-header">
                <h3 style="color: black !important;">Profile</h3>
            </div>
            <div class="card-body">
                @if(Session::has('error'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ Session::get('error') }}
                </div>
                {{ session()->forget('error') }}
                @endif
                @if(Session::has('success'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ Session::get('success') }}
                </div>
                {{ session()->forget('success') }}
                @endif
                <form action="{{ url('/profile') }}" id="frmCreate" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="role_id" value="{{ $data->role_id }}">
                    <div class="form-group">
                        <label for="image" class="col-sm-12">Image</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="file" name="image" id="image" />
                            </div>
                            <div class="col-sm-6">
                                <div class="holder pb-3">
                                    <div class="row">
                                        <span style="color: grey; font-size: 12px">This image preview resized to 120px, uploaded image is still using original dimensions.</span>
                                    </div>
                                    <img id="imgPreview" src="{{ $data->image }}" alt="pic" width="120" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Name" value="{{ $data->name }}" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Email" value="{{ $data->email }}" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                            <div class="input-group-append">
                                <span class="input-group-text reveal-password" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <span style="color: grey; font-size: 12px">Leave empty if you don't want to change password.</span>
                    </div>
                    <div class="form-group">
                        <label for="passwordConfirmation">New Password Confirmation</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="passwordConfirmation" placeholder="Password Confirmation" name="passwordConfirmation">
                            <div class="input-group-append">
                                <span class="input-group-text reveal-password" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>


                        <span style="color: grey; font-size: 12px">Leave empty if you don't want to change password.</span>
                    </div>
                    <button type="submit" class="btn mb-2 btn-primary btnSave">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(() => {
        const imageInp = $("#image");
        let imgURL;

        $('#formBtn').click((e) => {
            e.preventDefault();
            if (imgURL) {
                alert("The image is uploaded successfully!!");
            } else {
                alert("Please select a Image first!!");
            }

        });

        imageInp.change(function(e) {
            imgURL = URL.createObjectURL(e.target.files[0]);
            $("#imgPreview").attr("src", imgURL);
        });
    });

    $('.reveal-password').on('click', function() {
      if ($('#password').prop('type') == 'password') {
        $('#password').prop('type', 'text')
        $('.reveal-password i').removeClass('fa-eye')
        $('.reveal-password i').addClass('fa-eye-slash')
      } else {
        $('#password').prop('type', 'password')
        $('.reveal-password i').removeClass('fa-eye-slash')
        $('.reveal-password i').addClass('fa-eye')
      }
    })
    $('.reveal-passwordConfirmation').on('click', function() {
      if ($('#passwordConfirmation').prop('type') == 'password') {
        $('#passwordConfirmation').prop('type', 'text')
        $('.reveal-passwordConfirmation i').removeClass('fa-eye')
        $('.reveal-passwordConfirmation i').addClass('fa-eye-slash')
      } else {
        $('#passwordConfirmation').prop('type', 'password')
        $('.reveal-passwordConfirmation i').removeClass('fa-eye-slash')
        $('.reveal-passwordConfirmation i').addClass('fa-eye')
      }
    })
</script>
@endsection
