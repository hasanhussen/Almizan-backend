@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid py-2">
        @include('admin.partials.alerts')
        <div class="d-flex justify-content-between align-items-center mb-3 px-2">

            {{-- زر إضافة مستخدم --}}
            <button type="button" class="btn btn-info d-flex align-items-center gap-2"
                onclick="window.location='{{ route('users.create') }}'">
                <i class="bi bi-plus-lg"></i>
                Add New User
            </button>

            {{-- زر الترفيع (للأدمن فقط) --}}
            @if (auth()->user()->hasRole('admin'))
                <button type="button" class="btn btn-success d-flex align-items-center gap-2 shadow-sm me-3"
                    style="
        padding: 10px 18px;
        font-weight: 600;
        font-size: 0.95rem;
        border-radius: 10px;
        letter-spacing: 0.3px;
      "
                    data-bs-toggle="modal" data-bs-target="#promotionModal">
                    <i class="bi bi-mortarboard-fill fs-5"></i>
                    Promote to Next Academic Year
                </button>
            @endif

        </div>




        {{-- جدول الطلاب --}}
        <div class="card my-4">
            <div
                class="users-table-header shadow-dark border-radius-lg pt-4 pb-3 d-flex align-items-center justify-content-between">
                <h6 class="text-dark text-capitalize ps-3 mb-0">Students Table</h6>



                <div class="d-flex align-items-center">
                    <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-3">

                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="rounded border-0 p-2" style="background-color: rgba(210, 209, 209, 0);">
                            {{-- <label class="fw-bold d-block mb-1">⚡ revoked_statuses:</label> --}}
                            @php
                                $revoked_statuses = [
                                    '1' => 'Active',
                                    '2' => 'Revoked',
                                ];
                            @endphp

                            @foreach ($revoked_statuses as $key => $label)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="revoked_statuses[]"
                                        value="{{ $key }}"
                                        {{ is_array(request('revoked_statuses')) && in_array($key, request('revoked_statuses')) ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <label class="form-check-label">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>

                    </form>

                    {{-- 🔹 فلترة السنوات --}}
                    <form method="GET" class="mb-3">
                        <div class="rounded border-0 p-2 bg-light">
                            {{-- <label class="fw-bold d-block mb-1"> Years Filter:</label> --}}
                            @php
                                $years = ['1st', '2nd', '3rd', '4th'];
                            @endphp
                            @foreach ($years as $year)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="years[]"
                                        value="{{ $year }}"
                                        {{ is_array(request('years')) && in_array($year, request('years')) ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <label class="form-check-label">{{ $year }}</label>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>




            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0" id="students-table">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Image</th>
                                <th class="text-center">Year</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Revoked Reason</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td class="text-center">{{ $student->id ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('users.show', $student->id) }}">
                                            {{ $student->name ?? '-' }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $student->email ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <div>
                                            <img src="{{ $student->image ? asset('storage/' . $student->image) : asset('assets/img/default-avatar.png') }}"
                                                class="avatar avatar-sm me-3 border-radius-lg" alt="{{ $student->name }}">
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $student->year ?? '-' }}</td>
                                    <td class="text-center">{{ $student->revoked_status == '1' ? 'Active' : 'Revoked' }}</td>
                                    <td class="text-center">
                                        @if ($student->revoked_reason)
                                            @if (strlen($student->revoked_reason) <= 30)
                                                {{ $student->revoked_reason ?? '-' }}
                                            @else
                                                {{ \Illuminate\Support\Str::limit($student->revoked_reason, 30) }}
                                                <button type="button" class="btn btn-link p-0" data-bs-toggle="modal"
                                                    data-bs-target="#banReasonModal{{ $student->id }}">
                                                    More
                                                </button>

                                                {{-- Modal --}}
                                                <div class="modal fade" id="banReasonModal{{ $student->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Revoked Reason</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ $student->revoked_reason }}
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="{{ route('users.edit', $student->id) }}"
                                                class="btn btn-sm btn-primary m-0">
                                                Edit
                                            </a>

                                            <button type="button" class="btn btn-sm btn-warning btn-ban-user m-0"
                                                data-id="{{ $student->id }}"
                                                data-status="{{ $student->revoked_status }}">
                                                {{ $student->revoked_status == '1' ? 'Revoke' : 'Unrevoke' }}
                                            </button>

                                            <button type="button" class="btn btn-sm btn-danger btn-delete-student m-0"
                                                data-id="{{ $student->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <div class="modal fade" id="promotionModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('users.promote') }}">
                    @csrf
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-arrow-up-circle me-2"></i>
                                Academic Promotion
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-muted mb-3">
                                Students will be promoted based on the number of passed subjects
                            </p>


                            <label class="fw-semibold mb-1">
                                Allowed number of carried subjects:
                            </label>

                            <input type="number" name="carry_limit" class="form-control" min="0" max="11"
                                required placeholder="example: 4">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                Execute
                            </button>

                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="modal fade" id="banUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="banUserForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="banUserModalTitle"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>Select a quick reason:</label>
                            <select class="form-select mb-2" id="banUserQuickReason" aria-placeholder="اختر سبب سريع"
                                onchange="document.getElementById('banUserCustomReason').value=this.value">
                            </select>
                            <label>Or enter a custom reason:</label>
                            <textarea id="banUserCustomReason" name="revoked_reason" class="form-control" placeholder="Write the reason here..."
                                required></textarea>
                            <label>Ban duration (days):</label>
                            <input type="number" id="banUseruntil" name="revoked_until" class="form-control"
                                placeholder="أيام" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger" id="banUserModalBtn"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        {{-- جدول المشرفين والأساتذة يظهر فقط للادمن --}}
        @if (auth()->user()->hasRole('admin'))
            <div class="my-5"></div>
            <div class="card my-4">
                <div
                    class="users-table-header shadow-dark border-radius-lg pt-4 pb-3 d-flex align-items-center justify-content-between">
                    <h6 class="text-dark text-capitalize ps-3 mb-0">Supervisors & Teachers Table</h6>

                    {{-- فلترة حسب الدور --}}
                    <div class="d-flex align-items-center">
                        <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-3">

                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <div class="rounded border-0 p-2" style="background-color: rgba(210, 209, 209, 0);">
                                {{-- <label class="fw-bold d-block mb-1">🎭 roles:</label> --}}
                                @foreach ($roles as $role)
                                    @if ($role->name !== 'student')
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="roles[]"
                                                value="{{ $role->id }}"
                                                {{ is_array(request('roles')) && in_array($role->id, request('roles')) ? 'checked' : '' }}
                                                onchange="this.form.submit()">
                                            <label class="form-check-label">{{ $role->name }}</label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                        </form>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0" id="staff-table">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staffs as $staff)
                                    <tr>
                                        <td class="text-center">{{ $staff->id ?? '-' }}</td>
                                        <td class="text-center">
                                            {{ $staff->name ?? '-' }}
                                        </td>
                                        <td class="text-center">{{ $staff->email ?? '-' }}</td>
                                        <td class="text-center">{{ $staff->roles->pluck('name')->join(', ') }}</td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <a href="{{ route('users.edit', $staff->id) }}"
                                                    class="btn btn-sm btn-primary m-0">
                                                    Edit
                                                </a>

                                                <button type="button" class="btn btn-sm btn-danger btn-delete-staff m-0"
                                                    data-id="{{ $staff->id }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                {{ $staffs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script>
        // أزرار الحظر
        $(document).on('click', '.btn-ban-user', function() {
            const id = $(this).data('id');
            const status = $(this).data('status');

            const form = $('#banUserForm');
            const modal = $('#banUserModal');
            const title = $('#banUserModalTitle');
            const quick = $('#banUserQuickReason');
            const custom = $('#banUserCustomReason');
            const until = $('#banUseruntil');
            const btn = $('#banUserModalBtn');

            if (status == '1') {
                title.text('Student Ban Reason');
                btn.text('Ban');

                quick.html(`
        <option value="" selected hidden>Select a quick reason</option>
        <option value="Violation of rules">Violation of rules</option>
        <option value="Cheating">Cheating</option>
        <option value="Causing disturbance">Causing disturbance</option>
      `);
                form.attr('action', 'users/' + id + '/ban');
                custom.val('');
                until.val('');
                modal.modal('show');
            } else {
                // Unban → أرسل الطلب مباشرة بدون مودال
                $.ajax({
                    url: 'users/' + id + '/ban',
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                        //  showNotice('✅ تم إلغاء الحرمان');
                    },
                    error: function(err) {
                        console.error(err);
                        // showNotice('❌ حدث خطأ أثناء إلغاء الحرمان', true);
                    }
                });
            }
        });

        $(document).on('click', '.btn-delete-student', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // قم بإرسال request إلى backend لحذف المستخدم
                    $.ajax({
                        url: "{{ url('admin/users') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(err) {
                            alert('Error!');
                            console.error(err);
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-delete-staff', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // قم بإرسال request إلى backend لحذف المستخدم
                    $.ajax({
                        url: "{{ url('admin/users') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let message = 'An error occurred';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: message
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
