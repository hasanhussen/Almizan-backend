@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-warning">Edit Question</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('questions.update', $question->id) }}" method="POST">
                    @csrf
                    @method('Patch')

                    <div class="row g-3">

                        {{-- الامتحان --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Exam Name</label>
                            <select name="exam_id" class="form-select" required>
                                <option disabled>Select Exam</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}"
                                        {{ old('exam_id', $question->exam_id) == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- نص السؤال --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Question Text</label>
                            <input type="text" name="question_text" class="form-control"
                                value="{{ old('question_text', $question->question_text) }}" required>
                        </div>

                        {{-- الدرجة --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mark</label>
                            <input type="number" name="mark" class="form-control" min="0" step="0.01"
                                value="{{ old('mark', $question->mark) }}" required>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('questions.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Update Question
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
