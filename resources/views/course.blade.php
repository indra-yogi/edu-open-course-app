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
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="{{ asset('assets/img') }}/${course.thumbnail ?? 'default.jpg'}" class="card-img-top" alt="Course Thumbnail">
                                <div class="card-body">
                                    <h5 class="card-title">${course.title}</h5>
                                    <p class="text-muted small">Uploaded by: ${course.uploader ?? 'Unknown'}</p>
                                    <p class="card-text">${course.description.substring(0, 100)}${course.description.length > 100 ? '...' : ''}</p>
                                    <div class="mb-2">
                                        ${course.categories.map(cat => `<span class="badge bg-secondary me-1">${cat}</span>`).join('')}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-heart text-danger"></i> ${course.likes_count}</span>
                                        <button class="btn btn-sm btn-primary view-course-btn" data-id="${course.id}">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    $('#course-list').html(html);
                } else {
                    $('#course-list').html(
                        '<div class="col-12"><div class="alert alert-info">No courses found.</div></div>'
                    );
                }
            }).fail(function() {
                $('#course-list').html(
                    '<div class="col-12"><div class="alert alert-danger">Failed to load courses. Please try again later.</div></div>'
                );
            });

            // View course button click handler
            $(document).on('click', '.view-course-btn', function() {
                const courseId = $(this).data('id');

                if (!isLoggedIn) {
                    bootbox.dialog({
                        title: '<i class="fas fa-lock me-2"></i> Login Required',
                        message: `
                            <div class="text-center">
                                <p class="mb-3">You need to login to view course details.</p>
                                <i class="fas fa-sign-in-alt fa-3x text-primary mb-3"></i>
                                <p>Login to access all course materials and features.</p>
                            </div>
                        `,
                        buttons: {
                            cancel: {
                                label: '<i class="fas fa-times"></i> Close',
                                className: 'btn-secondary'
                            },
                            login: {
                                label: '<i class="fas fa-sign-in-alt"></i> Login Now',
                                className: 'btn-primary',
                                callback: function() {
                                    window.location.href = "{{ route('login') }}";
                                }
                            }
                        }
                    });
                    return;
                }

                // User is logged in - redirect to course detail with ID
                window.location.href = "{{ url('/course') }}/" + courseId;
            });
        });
    </script>
@endsection
