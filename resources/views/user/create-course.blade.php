@extends('layouts.app')

@section('title', 'Create Course')

@section('css')
    <style>
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background: #fff;
            width: 250px;
            transition: all 0.3s ease;
            border-right: 1px solid #ddd;
            padding: 1rem;
        }

        .sidebar.shrink {
            width: 70px;
            overflow: hidden;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 10px;
            display: block;
            transition: 0.2s;
        }

        .sidebar .nav-link:hover {
            background: #f2f2f2;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: #f9f9f9;
        }

        .toggle-btn {
            cursor: pointer;
            margin-bottom: 1rem;
        }

        .course-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .course-title {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Menu</h5>
            </div>
            <a href="#" class="nav-link"><i class="fas fa-home me-2"></i> <span class="link-text">Dashboard</span></a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Create A New Course</h3>
            </div>

            <form id="create-course-form" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Course Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Course Category</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Course Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="thumbnail" class="form-label">Thumbnail Image</label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label for="material_type" class="form-label">Material Type</label>
                    <select class="form-select" id="material_type" name="material_type" required>
                        <option value="">Select Material Type</option>
                        <option value="pdf">PDF</option>
                        <option value="video">YouTube Video</option>
                    </select>
                </div>

                <div class="mb-3" id="pdf-upload-group" style="display:none">
                    <label for="material_file" class="form-label">Upload PDF</label>
                    <input type="file" class="form-control" id="material_file" name="material_file"
                        accept="application/pdf">
                </div>

                <div class="mb-3" id="video-link-group" style="display:none">
                    <label for="material_link" class="form-label">YouTube Link</label>
                    <input type="url" class="form-control" id="material_link" name="material_link"
                        placeholder="https://youtube.com/..." />
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            <div id="create-course-message" class="mt-3"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const storeCourseUrl = "{{ route('api.course.store') }}";

        $('#material_type').on('change', function() {
            const type = $(this).val();

            if (type === 'pdf') {
                $('#pdf-upload-group').show();
                $('#video-link-group').hide();
                $('#material_link').val('');
            } else if (type === 'video') {
                $('#video-link-group').show();
                $('#pdf-upload-group').hide();
                $('#material_file').val('');
            } else {
                $('#pdf-upload-group').hide();
                $('#video-link-group').hide();
                $('#material_file').val('');
                $('#material_link').val('');
            }
        });

        $('#create-course-form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            formData.append('user_id', "{{ auth()->user()->id }}");

            $.ajax({
                url: storeCourseUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#create-course-message').html(
                        `<div class="alert alert-success">${response.message}</div>`
                    );
                    $('#create-course-form')[0].reset();
                    $('#pdf-upload-group, #video-link-group').addClass('d-none');
                },
                error: function(xhr) {
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorHtml += `<li>${value[0]}</li>`;
                    });
                    errorHtml += '</ul></div>';
                    $('#create-course-message').html(errorHtml);
                }
            });
        });
    </script>
@endsection
