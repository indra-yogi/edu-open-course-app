<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth') - OpenCourse</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @yield('css')
    <style>
        body {
            background: linear-gradient(to right, #92E3A9, #ffffff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
        }

        .form-control:focus {
            border-color: #92E3A9;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #92E3A9;
            border-color: #92E3A9;
        }

        .btn-primary:hover {
            background-color: #7ddda0;
            border-color: #7ddda0;
        }
    </style>
</head>

<body>

    <main class="auth-card shadow-sm">
        @yield('content')
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


    @yield('js')
</body>

</html>
