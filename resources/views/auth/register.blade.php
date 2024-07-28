<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-6">
                <div class="form-container text-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo mb-4">
                    <h1 class="mb-2">Get Started with a Free Account</h1>
                    <p class="lead mb-4">Sign up in seconds</p>
                    <form id="register-form">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter your Name" required>
                            <span class="text-danger" id="name-error"></span>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your Email" required>
                            <span class="text-danger" id="email-error"></span>
                        </div>
                        <div class="form-group position-relative">
                            <div class="password-container">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your Password" required>
                                <i class="fas fa-eye password-icon" onclick="togglePasswordVisibility('password')"></i>
                            </div>
                            <span class="text-danger" id="password-error"></span>
                        </div>
                        <div class="form-group position-relative">
                            <div class="password-container">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                            </div>
                            <span class="text-danger" id="password-confirmation-error"></span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                        <p class="mt-3">Already have an account? <a href="">Sign in</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var icon = passwordField.nextElementSibling;
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        $(document).ready(function () {
    $('#register-form').on('submit', function (e) {
        e.preventDefault();

        $('.text-danger').text(''); 

        var password = $('#password').val();
        var passwordConfirmation = $('#password_confirmation').val();

        if (password !== passwordConfirmation) {
            $('#password-confirmation-error').text('Passwords do not match.');
            return;
        }

        $.ajax({
            url: "{{ route('register') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    window.location.href = "{{ route('verify.otp.view') }}?email=" + encodeURIComponent(response.email);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#name-error').text(errors.name[0]);
                    }
                    if (errors.email) {
                        $('#email-error').text(errors.email[0]);
                    }
                    if (errors.password) {
                        $('#password-error').text(errors.password[0]);
                    }
                }
            }
        });
    });
});


    </script>
</body>

</html>
