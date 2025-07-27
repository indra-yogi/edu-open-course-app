@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <h2 class="text-center mb-4">Create an Account</h2>

    <form id="registerForm">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>

        <div class="text-center mt-3">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>
    </form>
@endsection

@section('js')
    <script>
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('register') }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    alert(response.message);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    let message = res.message || 'Registration failed';
                    if (res.errors) {
                        const errors = Object.values(res.errors).flat().join('\n');
                        message += '\n' + errors;
                    }
                    alert(message);
                }
            });
        });
    </script>
@endsection
