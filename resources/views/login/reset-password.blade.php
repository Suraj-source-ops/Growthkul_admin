<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('/assets/css/style.css')}}" />
    <link rel="shortcut icon" type="image/png" href="{{ url('/assets/images/fav-Icon.png') }}" />
    <title>Growthkul</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">

</head>

<body>
    <div class="login_main_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-lg-6 p-0">
                    <div class="login_left_box">
                        <div class="login-img">
                            <img src="{{ url('/assets/images/login-image.png') }}" alt="Login banner image">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 p-0">
                    <div class="login_right_box">
                        <div class="login_form_box">
                            <div class="head-box">
                                <a href="{{route('login')}}">
                                    <img src="{{ asset('/assets/images/back.png') }}" alt="Back Logo" />
                                </a>
                            </div>
                            <div class="forget-password">
                                <form action="{{ route('password.update') }}" method="post" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <h3 class="login-text">Reset Password</h3>
                                    <div class="login_input_box">
                                        <label>Password</label>
                                        <input type="password" name="password" placeholder="Enter New Password"
                                            required>
                                    </div>
                                    <div class="login_input_box">
                                        <label>Confirm Password</label>
                                        <input type="password" name="password_confirmation"
                                            placeholder="Confirm Password" required>
                                    </div>
                                    <button class="Sign_in_btn" type="submit">Reset Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        @if (Session::has('message'))
        toastr.options = {
            closeButton: true,
            preventDuplicates: true,
            timeOut: 3000
        };
        var type = "{{ Session::get('alert-type', 'info') }}";
        var message = "{{ Session::get('message') }}";
        switch (type) {
            case 'info':
                toastr.info(message);
                break;
            case 'success':
                toastr.success(message);
                break;
            case 'warning':
                toastr.warning(message);
                break;
            case 'error':
                toastr.error(message);
                break;
        }
    @endif
    </script>
    <script>
        $(".password_eye_icon").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            input = $(this).parent().find("input");
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
</body>

</html>