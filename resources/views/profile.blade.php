@extends('admin_template')

@section('title page')
    Profile
@endsection

@section('content')
    <style>
        .form-control-feedback {
            pointer-events: all;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: 3px solid #f0f0f0;
        }

        .file-label {
            font-size: 13px;
            color: #6c757d;
        }

        .custom-file-button {
            padding: 6px 12px;
            font-size: 14px;
            cursor: pointer;
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

                    <div class="row">
                        <!-- Bagian Foto Profil -->
                        <div class="col-md-4 text-center">
                            <label for="image">Profile Picture</label>
                            <div class="d-flex flex-column align-items-center">
                                <img id="imgPreview" src="{{ $data->image }}" alt="Profile Picture" class="profile-img mb-2">
                                
                                <div class="form-group text-center">
                                    <!-- Sembunyikan input file -->
                                    <input type="file" name="image" id="image" class="d-none">

                                    <!-- Label custom sebagai tombol -->
                                    <label for="image" class="btn btn-outline-primary btn-sm custom-file-button">Choose Image</label>
                                    
                                    <small class="file-label d-block mt-2">Image preview is 150px, original dimensions are kept.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Bagian Kanan -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Name"
                                       value="{{ $data->name }}" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Email"
                                       value="{{ $data->email }}" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="password">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" placeholder="Password"
                                           name="password">
                                    <div class="input-group-append">
                                        <span class="input-group-text reveal-password" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted">Leave empty if you don't want to change password.</small>
                            </div>

                            <div class="form-group">
                                <label for="passwordConfirmation">New Password Confirmation</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="passwordConfirmation"
                                           placeholder="Password Confirmation" name="passwordConfirmation">
                                    <div class="input-group-append">
                                        <span class="input-group-text reveal-passwordConfirmation" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted">Leave empty if you don't want to change password.</small>
                            </div>

                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-primary btnSave">Update</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(() => {
        const imageInp = $("#image");

        imageInp.change(function(e) {
            let imgURL = URL.createObjectURL(e.target.files[0]);
            $("#imgPreview").attr("src", imgURL);
        });
    });

    $('.reveal-password').on('click', function () {
        const input = $('#password');
        const icon = $(this).find('i');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        icon.toggleClass('fa-eye fa-eye-slash');
    });

    $('.reveal-passwordConfirmation').on('click', function () {
        const input = $('#passwordConfirmation');
        const icon = $(this).find('i');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        icon.toggleClass('fa-eye fa-eye-slash');
    });
</script>
@endsection
