@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">

        <h4 class="fw-bold text-danger mb-4">Add Student To Exam</h4>

        {{-- رسائل --}}
        @include('admin.partials.alerts')
        <a href="{{ route('students.index', $exam->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>

        <div class="card shadow-sm border-0">

            <div class="card-body">


                <form action="{{ route('students.store', $exam->id) }}" method="POST">
                    @csrf

                    {{-- Exam name --}}
                    <div class="mb-3">
                        <label class="form-label">Exam Name</label>
                        <input type="text" class="form-control" value="{{ $exam->subject->name }}" readonly>
                    </div>

                    {{-- Exam session --}}
                    <div class="mb-3">
                        <label class="form-label">Exam Session</label>
                        <input type="text" class="form-control" value="{{ $exam->exam_term }}" readonly>
                    </div>

                    {{-- Exam year --}}
                    <div class="mb-3">
                        <label class="form-label">Exam Year</label>
                        <input type="text" class="form-control" value="{{ $exam->year }}" readonly>
                    </div>

                    {{-- Select student --}}
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select student --</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- hidden exam_id --}}
                    <input type="hidden" name="exam_id" value="{{ $exam->id }}">

                    <div class="text-end">
                        <button class="btn btn-success">
                            Add Student
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
