@extends('layouts.app')

@section('title', 'Courses')

@section('css')
    <style>
        .course-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.2s ease;
        }

        .course-card:hover {
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
        }

        .course-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 6px;
        }

        .course-meta {
            font-size: 0.85rem;
            color: #666;
        }

        .category-badge {
            background-color: #45a861;
            color: #fff;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-size: 0.75rem;
            margin-right: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="container my-4">
        <h2 class="mb-4">Courses</h2>
        <div id="course-list" class="row">
            <!-- Courses will be appended here -->
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

            $.get("{{ route('api.course.index') }}", function(res) {
                if (res.status === 'success') {
                    let html = '';
                    res.data.forEach(course => {
                        html += `
                        <div class="col-md-4">
                            <div class="course-card">
                                <img src="{{ asset('assets/img') }}/${course.thumbnail ?? 'default.jpg'}" class="course-thumbnail mb-2" alt="Course Thumbnail">
                                <h5>${course.title}</h5>
                                <p class="course-meta">Uploaded by: ${course.uploader ?? 'Unknown'}</p>
                                <p>${course.description}</p>
                                <div class="mb-2">
                                    ${course.categories.map(cat => `<span class="category-badge">${cat}</span>`).join('')}
                                </div>
                                <p class="course-meta"><i class="fas fa-heart text-danger"></i> ${course.likes_count} likes</p>
                            </div>
                        </div>
                    `;
                    });
                    $('#course-list').html(html);
                } else {
                    $('#course-list').html('<p>No approved courses found.</p>');
                }
            }).fail(function() {
                $('#course-list').html('<p>Failed to load courses.</p>');
            });
        });

        $(document).on('click', '.view-course-btn', function() {
            const courseId = $(this).data('id');

            if (!isLoggedIn) {
                bootbox.dialog({
                    title: '<i class="fas fa-lock me-2 text-success"></i> Login Required',
                    message: `
                <div class="text-center">
                    <p class="mb-3">Please log in to access this course and continue learning with us.</p>
                    <i class="fas fa-user-lock fa-3x text-success mb-3"></i>
                </div>
            `,
                    buttons: {
                        cancel: {
                            label: '<i class="fas fa-times"></i> Cancel',
                            className: 'btn-secondary'
                        },
                        login: {
                            label: '<i class="fas fa-sign-in-alt"></i> Login Now',
                            className: 'btn-success',
                            callback: function() {
                                window.location.href = "{{ route('login') }}";
                            }
                        }
                    },
                    closeButton: false
                });

                return;
            }

            // User is logged in — redirect to course detail
            window.location.href = `/course/${courseId}`;
        });
    </script>
@endsection
