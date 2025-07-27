@extends('layouts.app')

@section('title', 'Course Detail')

@section('css')
    <style>
        .main-content {
            padding: 2rem;
            background-color: #f9f9f9;
            min-height: 100vh;
        }

        .course-thumbnail {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .course-info h4 {
            font-weight: bold;
        }

        .ratio-16x9 {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            height: 0;
            margin-top: 1rem;
        }

        .ratio-16x9 iframe {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
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
        <div class="mb-4">
            <a href="{{ route('courses') }}" class="btn btn-secondary">← Back to Courses</a>
        </div>
        <div id="course-detail">
            <p>Loading course details...</p>
        </div>

        <div class="container mt-4">
            <h4>Comments (<span id="comments-count">0</span>)</h4>
            <div id="comments-list" class="mt-3"></div>
        </div>

        <div class="mt-4">
            <h5>Leave a Comment</h5>
            <form id="comment-form">
                <input type="hidden" id="parent_id" value="">
                <div class="form-group mb-2">
                    <textarea class="form-control" name="comment" id="comment-content" rows="3" placeholder="Write your comment..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const courseId = {{ $id }};
        const apiUrl = "{{ route('api.course.show', ['id' => '__ID__']) }}".replace('__ID__', courseId);

        $(document).ready(function() {
            loadCourse(courseId);

            function renderComment(comment, indent = 0) {
                const marginLeft = indent * 20;
                let html = `
                <div class="border rounded p-3 mb-2" style="margin-left:${marginLeft}px">
                    <strong>${comment.user}</strong>
                    <p class="mb-1">${comment.comment}</p>
                    <small class="text-muted">${comment.created_at}</small>
                    <br>
                    <button class="btn btn-sm btn-outline-secondary reply-btn mt-2" data-parent-id="${comment.id}">Reply</button>
                    <div class="reply-form mt-2" id="reply-form-${comment.id}"></div>
                </div>
            `;

                if (comment.replies && comment.replies.length > 0) {
                    comment.replies.forEach(reply => {
                        html += renderComment(reply, indent + 1);
                    });
                }

                return html;
            }

            function loadCourse(courseId) {
                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(res) {
                        if (res.status === 'success' && res.data) {
                            const course = res.data;
                            const thumbnail = course.thumbnail ?? 'default-thumbnail.jpg';

                            let html = `
                            <img src="{{ asset('assets/img') }}/${thumbnail}" alt="${course.title}" class="course-thumbnail" />

                            <div class="course-info">
                                <h4>${course.title}</h4>
                                <p class="text-muted mb-2"><strong>Category:</strong> ${course.categories}</p>
                                <p><strong>Description:</strong></p>
                                <p>${course.description}</p>
                                <p><strong>Uploaded by:</strong> ${course.creator}</p>
                                <p><strong>Likes:</strong> ${course.likes_count} | <strong>Comments:</strong> ${course.comments_count}</p>
                            </div>

                            <div class="mt-4">
                                <h5>Materials</h5>
                                <ul class="list-group">`;

                            course.materials.forEach(material => {
                                html += `<li class="list-group-item mb-3">
                                <strong>${material.title}</strong> (${material.file_type})<br>`;

                                if (material.file_type === 'video') {
                                    try {
                                        html += `
                                        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                                            <iframe src="${material.path_file}" frameborder="0" allowfullscreen
                                                style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
                                        </div>`;
                                    } catch (e) {
                                        html +=
                                            `<a href="${material.content}" target="_blank">Watch Video</a>`;
                                    }
                                } else if (material.file_type === 'pdf') {
                                    html +=
                                        `<a href="{{ asset('storage/files') }}/${material.content}" target="_blank">Download PDF</a>`;
                                }

                                html += `</li>`;
                            });

                            html += `</ul></div>`;
                            $('#course-detail').html(html);

                            $('#comments-count').text(res.data.comments_count);

                            let commentsHtml = '';
                            if (res.data.comments.length > 0) {
                                res.data.comments.forEach(comment => {
                                    commentsHtml += renderComment(comment);
                                });
                            } else {
                                commentsHtml = `<p class="text-muted">No comments yet.</p>`;
                            }

                            $('#comments-list').html(commentsHtml);
                        } else {
                            $('#course-detail').html(
                                '<div class="alert alert-info">Course not found.</div>');
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        $('#course-detail').html(
                            '<div class="text-danger">Failed to load course data.</div>');
                    }
                });
            }

            // Handle new comment form
            $('#comment-form').on('submit', function(e) {
                e.preventDefault();

                const content = $('#comment-content').val().trim();
                const parentId = $('#parent_id').val();

                if (content === '') return alert('Comment cannot be empty.');

                const formData = new FormData();
                formData.append('comment', content);
                formData.append('user_id', "{{ auth()->user()->id }}");
                if (parentId) formData.append('parent_id', parentId);

                $.ajax({
                    url: '{{ url('/api/course') }}/' + courseId + '/comment',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#comment-content').val('');
                        $('#parent_id').val('');
                        $('.reply-form').empty(); // clear any reply boxes
                        loadCourse(courseId);
                    },
                    error: function(err) {
                        alert('Failed to post comment.');
                    }
                });
            });

            // Handle reply button click (add inline form)
            $(document).on('click', '.reply-btn', function() {
                const parentId = $(this).data('parent-id');
                const replyBox = `
                <div class="form-group mt-2">
                    <textarea class="form-control mb-2 reply-textarea" rows="2" placeholder="Write a reply..."></textarea>
                    <button class="btn btn-sm btn-primary post-reply-btn" data-parent-id="${parentId}">Reply</button>
                </div>
            `;

                $('.reply-form').empty(); // only allow one reply box open
                $(`#reply-form-${parentId}`).html(replyBox);
            });

            // Handle reply form submission
            $(document).on('click', '.post-reply-btn', function() {
                const parentId = $(this).data('parent-id');
                const content = $(`#reply-form-${parentId} .reply-textarea`).val().trim();

                if (content === '') return alert('Reply cannot be empty.');

                const formData = new FormData();
                formData.append('comment', content);
                formData.append('user_id', "{{ auth()->user()->id }}");
                formData.append('parent_id', parentId);

                $.ajax({
                    url: '{{ url('/api/course') }}/' + courseId + '/comment',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        loadCourse(courseId);
                    },
                    error: function() {
                        alert('Failed to post reply.');
                    }
                });
            });
        });
    </script>
@endsection
