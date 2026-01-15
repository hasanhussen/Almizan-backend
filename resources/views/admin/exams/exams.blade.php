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

        .icon-action {
            font-size: 18px;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .icon-action:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection

@section('content')
    <div class="container py-3">
        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-danger">Exams Management</h4>
            <a href="{{ route('exams.create') }}" class="btn btn-info">
                <i class="bi bi-plus-lg"></i> Add New Exam
            </a>
        </div>

        {{-- 🔹 فلترة السنوات --}}
        <form method="GET" class="mb-3">
            <div class="rounded border-0 p-2 bg-light">
                <label class="fw-bold d-block mb-1"> Years Filter:</label>
                @php
                    $years = ['1st', '2nd', '3rd', '4th'];
                @endphp
                @foreach ($years as $year)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="years[]" value="{{ $year }}"
                            {{ is_array(request('years')) && in_array($year, request('years')) ? 'checked' : '' }}
                            onchange="this.form.submit()">
                        <label class="form-check-label">{{ $year }}</label>
                    </div>
                @endforeach
            </div>
        </form>

        {{-- 🔹 جدول الامتحانات --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Subject</th>
                            <th class="text-center">Year</th>
                            <th class="text-center">Exam Date</th>
                            <th class="text-center">Exam Type</th>
                            <th class="text-center">Exam Term</th>
                            <th class="text-center">Start Date</th>
                            <th class="text-center">End Date</th>
                            <th class="text-center">Actual Duration</th>
                            <th class="text-center">Total Marks</th>
                            <th class="text-center">Success Rate</th>
                            <th class="text-center">Exam Status</th>
                            <th class="text-center" style="min-width: 260px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr>
                                <td class="text-center">{{ $exam->subject->name ?? '-' }}</td>
                                <td class="text-center">{{ $exam->year ?? '-' }}</td>
                                <td class="text-center">
                                    {{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td class="text-center">{{ $exam->exam_type ?? '-' }}</td>
                                <td class="text-center">{{ $exam->exam_term ?? '-' }}</td>
                                <td class="text-center">
                                    {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '-' }}
                                    <button type="button" class="btn btn-sm btn-link text-primary p-0 border-0"
                                        data-bs-toggle="modal" data-bs-target="#editDateModal-{{ $exam->id }}">
                                        <i class="bi bi-calendar text-secondary" style="font-size: 12px;"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    {{ $exam->end_time ? \Carbon\Carbon::parse($exam->end_time)->format('H:i') : '-' }}
                                    <button type="button" class="btn btn-sm btn-link text-primary p-0 border-0"
                                        data-bs-toggle="modal" data-bs-target="#editDateModal-{{ $exam->id }}">
                                        <i class="bi bi-calendar text-secondary" style="font-size: 12px;"></i>
                                    </button>
                                </td>
                                <td class="text-center">{{ $exam->actual_duration . ' minutes ' ?? '-' }}</td>

                                <td class="text-center">{{ $exam->total_marks ?? '-' }}</td>
                                <td class="text-center">{{ $exam->success_rate ?? '-' }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge text-white
                                @if ($exam->exam_status == '0') bg-secondary
                                @elseif($exam->exam_status == '1') bg-warning
                                @elseif($exam->exam_status == '2') bg-success
                                @else bg-dark @endif">
                                        @if ($exam->exam_status == '0')
                                            Not started yet
                                        @elseif($exam->exam_status == '1')
                                            In progress
                                        @elseif($exam->exam_status == '2')
                                            Completed
                                        @else
                                            Unknown
                                        @endif
                                    </span>
                                </td>

                                {{-- 🔹 أزرار الإجراءات --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        @if ($exam->exam_status == '0')
                                            {{-- Edit --}}
                                            <a href="{{ route('exams.edit', $exam->id) }}"
                                                class="icon-action text-secondary" data-bs-toggle="tooltip"
                                                title="Edit Exam">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ route('exams.destroy', $exam->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="icon-action text-danger border-0 bg-transparent"
                                                    data-bs-toggle="tooltip" title="Delete Exam">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            {{-- Start --}}
                                            <form action="{{ route('exams.startExam', $exam->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="icon-action text-success border-0 bg-transparent"
                                                    data-bs-toggle="tooltip" title="Start Exam">
                                                    <i class="bi bi-play-fill"></i>
                                                </button>
                                            </form>

                                            {{-- Students --}}
                                            <a href="{{ route('students.index', $exam->id) }}"
                                                class="icon-action text-secondary" data-bs-toggle="tooltip"
                                                title="Students List">
                                                <i class="bi bi-people-fill"></i>
                                            </a>
                                        @elseif($exam->exam_status == '1')
                                            {{-- End --}}
                                            <form action="{{ route('exams.endExam', $exam->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="icon-action text-warning border-0 bg-transparent"
                                                    data-bs-toggle="tooltip" title="End Exam">
                                                    <i class="bi bi-stop-fill"></i>
                                                </button>
                                            </form>

                                            {{-- Students --}}
                                            <a href="{{ route('students.index', $exam->id) }}"
                                                class="icon-action text-secondary" data-bs-toggle="tooltip"
                                                title="Students List">
                                                <i class="bi bi-people-fill"></i>
                                            </a>
                                        @elseif($exam->exam_status == '2')
                                            {{-- Results --}}
                                            <a href="{{ route('results.exam', $exam->id) }}" class="icon-action text-info"
                                                data-bs-toggle="tooltip" title="View Results">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            {{-- Students --}}
                                            <a href="{{ route('students.index', $exam->id) }}"
                                                class="icon-action text-secondary" data-bs-toggle="tooltip"
                                                title="Students List">
                                                <i class="bi bi-people-fill"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>



                            </tr>

                            {{-- 🔹 نافذة تعديل التواريخ --}}
                            <div class="modal fade" id="editDateModal-{{ $exam->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('exams.updateDate', $exam->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Dates for {{ $exam->subject->name }}</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Start Date:</label>
                                                    <input type="time" name="start_time" class="form-control"
                                                        value="{{ old('start_time', $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">End Date:</label>
                                                    <input type="time" name="end_time" class="form-control"
                                                        value="{{ old('end_time', $exam->end_time ? \Carbon\Carbon::parse($exam->end_time)->format('H:i') : '') }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-3">No exams found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $exams->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el)
        })
    </script>
@endsection
