@extends('admin.layouts.master')

@section('content')
    <div class="container py-3">

        @include('admin.partials.alerts')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-danger">Results {{ $exam->subject->name ?? '-' }} Management</h4>
        </div>

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
                                <td>{{ $result->user->id }}</td>

                                <td>
                                    <h6 class="mb-0 text-sm">
                                        {{ $result->user->name }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <span class="text-sm">
                                        {{ $result->subject->name }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    {{ $result->exam->exam_type }}
                                </td>

                                <td class="text-center">
                                    {{ $result->exam->exam_term }}
                                </td>

                                <td class="text-center fw-bold">
                                    {{ $result->exam_degree }}
                                </td>

                                <td class="text-center">
                                    @if ($result->is_passed)
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
