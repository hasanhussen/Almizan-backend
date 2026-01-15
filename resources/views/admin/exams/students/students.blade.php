@extends('admin.layouts.master')

@section('css')
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .table td,
        .table th {
            white-space: nowrap;
            vertical-align: middle;
        }

        .action-btn {
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
        }

        .action-btn i {
            font-size: 16px;
            transition: transform 0.2s;
        }

        .action-btn:hover i {
            transform: scale(1.2);
        }
    </style>
@endsection

@section('content')
    <div class="container py-3">
        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-danger">
                Students Eligible for Exam: {{ $exam->subject->name ?? '-' }}
            </h4>
            @if ($exam->exam_status == '0')
                <a href="{{ route('students.create', $exam->id) }}" class="btn btn-info">
                    <i class="bi bi-plus-lg"></i> Add Student to Exam
                </a>
            @endif
            {{-- زر العودة لصفحة الامتحانات --}}
            {{-- <a href="{{ route('exams.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Exams
        </a> --}}
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form action="{{ route('students.generateCodes', $exam->id) }}" method="POST">
                @csrf
                <button type="submit" class="action-btn text-primary" title="Generate Code">
                    <i class="bi bi-key-fill"></i>
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Student ID</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Subject</th>
                            <th class="text-center">Exam Type</th>
                            <th class="text-center">Exam Term</th>
                            <th class="text-center">Code</th>
                            <th class="text-center">Submitted</th>
                            <th class="text-center" style="min-width: 240px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $userExam)
                            <tr>
                                <td class="text-center">{{ $userExam->user->id ?? '-' }}</td>
                                <td class="text-center">{{ $userExam->user->name ?? '-' }}</td>
                                <td class="text-center">{{ $exam->subject->name ?? '-' }}</td>
                                <td class="text-center">{{ $exam->exam_type ?? '-' }}</td>
                                <td class="text-center">{{ $exam->exam_term ?? '-' }}</td>
                                <td class="text-center">{{ $userExam->code ?? '#' }}</td>
                                <td class="text-center">
                                    @if ($userExam->revoked_status == '2')
                                        <span class="badge bg-warning">Revoked</span>
                                    @elseif($userExam->is_submitted == '1' && $userExam->revoked_status == '1')
                                        <span class="badge bg-success">Submitted</span>
                                    @elseif ($userExam->is_submitted == '0' && $userExam->revoked_status == '1')
                                        <span class="badge bg-secondary">Not Submitted</span>
                                    @else
                                        <span class="badge bg-info">Ended</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">


                                        @if ($exam->exam_status == '1' && $userExam->revoked_status == '1')
                                            {{-- إعادة الامتحان --}}
                                            <form action="{{ route('students.resetExam', $userExam->id) }}" method="POST"
                                                class="confirm-action-form">
                                                @csrf
                                                <button type="submit" class="action-btn text-warning" title="Reset Exam">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>


                                            {{-- إنهاء الامتحان --}}
                                            <form action="{{ route('students.endExam', $userExam->id) }}" method="POST"
                                                class="confirm-action-form">
                                                @csrf
                                                <button type="submit" class="action-btn text-danger" title="End Exam">
                                                    <i class="bi bi-stop-fill"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($userExam->revoked_status == '1')
                                            {{-- حرمان الطالب --}}
                                            <form action="{{ route('students.banExam', $userExam->id) }}" method="POST"
                                                class="confirm-action-form">
                                                @csrf
                                                <button type="submit" class="action-btn text-dark" title="Ban Student">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($exam->exam_status == '0')
                                            {{-- Delete --}}
                                            <form
                                                action="{{ route('students.destroy', ['student' => $userExam->id, 'exam' => $exam->id]) }}"
                                                method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="icon-action text-danger border-0 bg-transparent"
                                                    data-bs-toggle="tooltip" title="Delete Exam">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // جميع الفورمز اللي فيها أكشن حساس
            document.querySelectorAll('.confirm-action-form, .icon-action').forEach(function(formEl) {
                // حسب نوع العنصر: form أو button
                const isForm = formEl.tagName === 'FORM';

                const button = isForm ?
                    formEl.querySelector('button') :
                    formEl; // زر Delete خارج confirm-action-form

                const parentForm = isForm ? formEl : formEl.closest('form');

                if (!button || !parentForm) return;

                parentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    let actionTitle = button.title || button.dataset.bsToggle || 'Confirm Action';
                    let message = '';

                    // تخصيص الرسالة حسب الزر
                    switch (actionTitle) {
                        case 'Delete Exam':
                        case 'Delete':
                            message =
                                'هل أنت متأكد من حذف هذا الطالب من الامتحان؟ لا يمكن التراجع عن هذا الإجراء.';
                            break;
                        case 'Ban Student':
                            message = 'هل أنت متأكد من حرمان هذا الطالب من الامتحان؟';
                            break;
                        case 'Reset Exam':
                            message = 'هل أنت متأكد من إعادة الامتحان لهذا الطالب؟';
                            break;
                        case 'End Exam':
                            message = 'هل أنت متأكد من إنهاء الامتحان لهذا الطالب؟';
                            break;
                        default:
                            message = 'هل أنت متأكد من متابعة هذا الإجراء؟';
                    }

                    Swal.fire({
                        title: actionTitle,
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، نفّذ!',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            parentForm.submit(); // تنفيذ الفورم إذا ضغط Yes
                        }
                    });
                });
            });
        });
    </script>
@endsection
