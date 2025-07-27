@extends('layouts.app')

@section('title', 'User Dashboard')

@section('css')
    <style>
        .main-content {
            padding: 2rem;
            background-color: #f9f9f9;
            min-height: 100vh;
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

        @media (max-width: 767.98px) {
            .main-content {
                padding: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="mb-2 mb-md-0">Uploaded Courses</h3>
            <a href="{{ route('course.create') }}" class="btn btn-primary">+ Add New Course</a>
        </div>

        <div id="approved-courses" class="container-fluid">
            <!-- Courses will be loaded here via AJAX -->
        </div>
    </div>
@endsection

@section('js')
    <script>
        const approvedCourseUrl = "{{ route('api.course.approved') }}";
        const loggedInUserId = {{ auth()->id() ?? 'null' }};

        $(document).ready(function() {
            if (!loggedInUserId) {
                $('#approved-courses').html('<p>You must be logged in to view your courses.</p>');
                return;
            }

            $.ajax({
                url: approvedCourseUrl,
                method: 'GET',
                data: {
                    user_id: loggedInUserId
                },
                success: function(res) {
                    if (res.status === 'success' && res.data.length) {
                        let html = '';
                        for (let i = 0; i < res.data.length; i += 2) {
                            html += '<div class="row mb-4">';

                            const course1 = res.data[i];
                            html += buildCardColumn(course1);

                            if (i + 1 < res.data.length) {
                                const course2 = res.data[i + 1];
                                html += buildCardColumn(course2);
                            }

                            html += '</div>';
                        }
                        $('#approved-courses').html(html);
                    } else {
                        $('#approved-courses').html(
                            '<div class="alert alert-info">No approved courses found.</div>'
                        );
                    }
                },
                error: function(err) {
                    console.error(err);
                    $('#approved-courses').html(
                        '<div class="text-danger">Failed to load courses.</div>');
                }
            });

            function buildCardColumn(course) {
                const thumbnail = course.thumbnail ? course.thumbnail : 'default-thumbnail.jpg';
                return `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ asset('assets/img') }}/${thumbnail}" class="card-img-top" alt="${course.title}" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">${course.title}</h5>
                                <p class="card-text">${course.description}</p>
                                <p><strong>Likes:</strong> ${course.likes_count} | <strong>Comments:</strong> ${course.comments_count}</p>
                                <p><strong>Categories:</strong> ${course.categories.join(', ')}</p>
                                <a href="{{ url('/course') }}/${course.id}" class="btn btn-sm btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
    </script>
@endsection
