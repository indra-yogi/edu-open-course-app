@extends('layouts.auth')

@section('title', 'Login')

@section('css')
    <style>
        body {
            background: linear-gradient(to right, #92E3A9, #ffffff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
        }

        .login-card h4 {
            margin-bottom: 1.5rem;
            font-weight: bold;
            text-align: center;
        }

        .form-control:focus {
            border-color: #30c55a;
            box-shadow: none;
        }

        .btn-success {
            background-color: #2ab451;
            border-color: #92E3A9;
        }

        .btn-success:hover {
            background-color: #45d178;
            border-color: #7ddda0;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="login-card">
            <h4><i class="fas fa-sign-in-alt me-2 text-success"></i> Login</h4>
            <form id="loginForm" method="POST" action="">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label"><i class="fas fa-lock me-1"></i>Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>

                <div class="text-center mt-3">
                    <small>Don't have an account? <a href="{{ route('home') }}">Register</a></small>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let data = form.serialize();
                let button = form.find('button[type=submit]');
                button.prop('disabled', true).text('Logging in...');

                $.ajax({
                    url: "{{ route('auth.login') }}",
                    type: "POST",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            if (res.redirect) {
                                window.location.href = res.redirect;
                            } else {
                                location.reload(); // fallback if no redirect
                            }
                        } else {
                            alert(res.message || 'Login failed');
                        }
                    },
                    error: function(xhr) {
                        let error = xhr.responseJSON?.message || 'An error occurred';
                        alert(error);
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Login');
                    }
                });
            });
        });
    </script>
@endsection
