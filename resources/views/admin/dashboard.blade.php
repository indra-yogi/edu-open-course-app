@extends('layouts.app')

@section('css')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Courses Management</h4>
            </div>
            <div class="card-body">
                <table id="coursesTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Uploader</th>
                            <th>Categories</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Materials</th>
                            <th class="text-center">Likes</th>
                            <th class="text-center">Comments</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- jQuery, Bootstrap, DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#coursesTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.courses.index') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'title',
                        render: function(data, type, row) {
                            return `<a href="{{ url('admin/courses') }}/${row.id}">${data}</a>`;
                        }
                    },
                    {
                        data: 'uploader'
                    },
                    {
                        data: 'categories'
                    },
                    {
                        data: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'materials_count',
                        className: 'text-center'
                    },
                    {
                        data: 'likes_count',
                        className: 'text-center'
                    },
                    {
                        data: 'comments_count',
                        className: 'text-center'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            const approveBtn = row.is_approved ?
                                `<button class="btn btn-sm btn-danger unapprove-btn" data-id="${data}">
                                    <i class="bi bi-hand-thumbs-down"></i> Unapprove
                                </button>` :
                                `<button class="btn btn-sm btn-success approve-btn" data-id="${data}">
                                    <i class="bi bi-hand-thumbs-up"></i> Approve
                                </button>`;

                            return `
                                <div class="btn-group">
                                    ${approveBtn}
                                    <a href="{{ url('admin/courses') }}/${data}/edit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            `;
                        },
                        orderable: false,
                        className: 'text-center'
                    }
                ]
            });

            // Approve/Unapprove action
            $(document).on('click', '.approve-btn, .unapprove-btn', function() {
                const courseId = $(this).data('id');
                const isApproving = $(this).hasClass('approve-btn');
                const $button = $(this);
                const $row = $button.closest('tr');

                $.ajax({
                    url: "{{ url('api/admin/courses') }}/" + courseId + "/approve",
                    method: 'PUT',
                    data: {
                        status: isApproving ? 1 : 0, // Explicit 1/0 for boolean
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update status badge
                            $row.find('.status-badge').html(response.status_badge);

                            // Toggle button state
                            const newButton = response.is_approved ?
                                `<button class="btn btn-sm btn-danger unapprove-btn" data-id="${courseId}">
                        <i class="bi bi-hand-thumbs-down"></i> Unapprove
                       </button>` :
                                `<button class="btn btn-sm btn-success approve-btn" data-id="${courseId}">
                        <i class="bi bi-hand-thumbs-up"></i> Approve
                       </button>`;

                            $button.replaceWith(newButton);

                            // Show toast notification
                            const toastMsg = response.is_approved ?
                                'Course approved successfully' :
                                'Course unapproved successfully';
                            showToast(toastMsg);
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Action failed', 'danger');
                    }
                });
            });

            // Toast helper function
            function showToast(message, type = 'success') {
                const toastEl = document.getElementById('actionToast');
                toastEl.querySelector('.toast-body').textContent = message;
                toastEl.classList.add(`bg-${type}`);
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }

            // Delete action
            $(document).on('click', '.delete-btn', function() {
                const courseId = $(this).data('id');

                const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                document.getElementById('confirmButton').onclick = function() {
                    $.ajax({
                        url: "{{ url('admin/courses') }}/" + courseId,
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            table.ajax.reload();
                            // Bootstrap toast notification
                            const toast = new bootstrap.Toast(document.getElementById(
                                'actionToast'));
                            document.getElementById('toastMessage').innerText =
                                'Course deleted successfully!';
                            toast.show();
                        }
                    });
                    confirmModal.hide();
                };
                confirmModal.show();
            });
        });
    </script>

    <!-- Bootstrap Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this course?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmButton" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
