@extends('admin.layouts.master')

@section('content')
    <div class="container py-3">

        @include('admin.partials.alerts')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-danger">Results Management</h4>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="d-flex flex-column gap-3 px-3 mb-3">

            {{-- ✅ الاحتفاظ بالبحث --}}
            <input type="hidden" name="search" value="{{ request('search') }}">

            {{-- فلترة حسب دورة الامتحان --}}
            <div class="rounded border-0 p-2 " style="background-color: rgba(210, 209, 209, 0);">
                <label class="fw-bold d-block mb-1">📂 Exam Terms:</label>
                <select name="exam_term" class="form-select" onchange="this.form.submit()">
                    <option value="" selected>All Exam Terms</option>
                    @foreach ($examTerms as $examTerm)
                        <option value="{{ $examTerm }}" {{ request('exam_term') == $examTerm ? 'selected' : '' }}>
                            {{ $examTerm }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- فلترة حسب نوع الامتحان --}}
            <div class="rounded border-0 p-2 " style="background-color: rgba(210, 209, 209, 0);">
                <label class="fw-bold d-block mb-1">📂 Exam Types:</label>
                <select name="exam_type" class="form-select" onchange="this.form.submit()">
                    <option value="" selected>All Exam Types</option>
                    @foreach ($examTypes as $examType)
                        <option value="{{ $examType }}" {{ request('exam_type') == $examType ? 'selected' : '' }}>
                            {{ $examType }}
                        </option>
                    @endforeach
                </select>
            </div>



        </form>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th class="text-center">Student</th>
                            <th class="text-center">Subject</th>
                            <th class="text-center">Exam Type</th>
                            <th class="text-center">Exam Term</th>
                            <th class="text-center">Mark</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>
                                <td>{{ $result->user->id ?? '-' }}</td>

                                <td class="text-center">
                                    <h6 class="mb-0 text-sm">
                                        {{ $result->user->name ?? '-' }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <span class="text-sm">
                                        {{ $result->subject->name ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    {{ $result->exam->exam_type ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $result->exam->exam_term ?? '-' }}
                                </td>

                                <td class="text-center fw-bold">
                                    @role(['admin', 'teacher'])
                                        <form action="{{ route('results.updateMark', $result->id) }}" method="POST"
                                            class="d-flex justify-content-center gap-1">
                                            @csrf
                                            @method('PATCH')

                                            <input type="number" name="exam_degree" value="{{ $result->exam_degree }}"
                                                min="0" max="{{ $result->exam->total_marks }}"
                                                class="form-control form-control-sm" style="width:80px">

                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{ $result->exam_degree }}
                                    @endrole
                                </td>

                                <td class="text-center">
                                    @if ($result->exam_degree >= $result->exam->success_rate)
                                        <span class="badge bg-success">Passed</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>


                                <td class="text-center">
                                    <a href="{{ route('results.answers', ['userId' => $result->user->id, 'examId' => $result->exam_id]) }}"
                                        class="btn btn-sm btn-info d-flex align-items-center justify-content-center">
                                        <i class="bi bi-eye me-1"></i> View Answers
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    No results found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $results->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>

    </div>
@endsection
