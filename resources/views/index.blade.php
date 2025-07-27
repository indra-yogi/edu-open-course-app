@extends('layouts.app')

@section('css')
    <style>
        body {
            background-color: #fffffc;
            background-size: cover;
        }

        .landing-wrapper {
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.4);
            border-radius: 1rem;
            padding: 2rem 1.5rem;
        }

        .landing-title {
            font-weight: 700;
            font-size: 2.5rem;
        }

        .landing-description {
            font-size: 1.1rem;
            color: #333;
        }

        .landing-image {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .landing-title {
                font-size: 1.8rem;
            }

            .landing-description {
                font-size: 1rem;
            }

            .landing-wrapper {
                text-align: center;
            }
        }
    </style>
@endsection
@section('content')
    {{-- PEMROGRAMAN WEB II
        Kelompok:
        1. Indra Yogi Prasetya
        2. Muhammad Fadilah Fauzan
    --}}
    <div class="landing-wrapper mt-5">
        <div class="row align-items-center">
            <!-- Left: Text -->
            <div class="col-md-6 mb-4 mb-md-0">
                <h1 class="landing-title mb-3">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Welcome to Open Course
                </h1>
                <p class="landing-description mb-4">
                    Discover a world of learning through our free and open online courses.
                    Gain new skills, advance your career, and explore your passion — anytime, anywhere.
                </p>
                <a href="/courses" class="btn btn-primary btn-lg">
                    <i class="fas fa-book-open me-2"></i> Browse Courses
                </a>
            </div>

            <!-- Right: Image -->
            <div class="col-md-6 text-center">
                <img src="{{ asset('assets/img/edu-1.png') }}" alt="Online Learning" class="landing-image">
            </div>
        </div>
        <section class="container py-5">
            <h2 class="mb-4">Top Courses</h2>
            <div class="row g-4">
                @foreach ($topCourses as $course)
                    <div class="col-md-4">
                        <div class="card h-100 border-0">
                            <img src="{{ $course->image_url }}" class="card-img-top" alt="{{ $course->title }}">
                            <div class="card-body px-3 py-2">
                                <h5 class="card-title mb-1">{{ $course->title }}</h5>
                                <p class="card-text mb-1 text-muted">Category: {{ $course->category }}</p>
                                <p class="card-text mb-3 text-muted">Uploaded by: <strong>{{ $course->uploader }}</strong>
                                </p>
                                <button type="button" class="btn btn-success w-100 view-course-btn">
                                    <i class="fas fa-sign-in-alt me-1"></i> View Course
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootbox@5.5.2/bootbox.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.view-course-btn').on('click', function() {
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
            });
        });
    </script>
@endsection
