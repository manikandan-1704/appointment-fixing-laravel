<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
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
                    <h1 class="mb-2">Verify Your Email</h1>
                    <p class="lead mb-4">Enter the OTP sent to your email</p>
                    <form id="verify-otp-form">
                        @csrf
                        <input type="hidden" name="email" id="email" value="{{ request()->query('email') }}">
                        <div class="form-group">
                            <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP" required>
                            <span class="text-danger" id="otp-error"></span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Verify OTP</button>
                    </form>
                    <p class="mt-3">Didn't receive the OTP? <a href="#" id="resend-otp-link">Resend</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#verify-otp-form').on('submit', function (e) {
                e.preventDefault();

                $('.text-danger').text(''); 

                $.ajax({
                    url: "{{ route('verify.otp') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            alert('OTP verified successfully!');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.otp) {
                                $('#otp-error').text(errors.otp[0]);
                            }
                        } else if (xhr.status === 401) {
                            $('#otp-error').text('Invalid or expired OTP');
                        }
                    }
                });
            });

            $('#resend-otp-link').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('resend.otp') }}",
                    method: 'POST',
                    data: {
                        email: $('#email').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('A new OTP has been sent to your email.');
                        }
                    },
                    error: function (xhr) {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });
        });
    </script>
</body>

</html>
