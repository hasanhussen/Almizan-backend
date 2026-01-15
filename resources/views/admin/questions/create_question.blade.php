@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-danger">Create New Question</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                {{-- لازم نحط enctype لأن فيه رفع ملف --}}
                <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        {{-- الاسم --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Exam Name</label>
                            <select name="exam_id" class="form-select" required>
                                <option value="" disabled selected>Select Exam</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- نص السؤال --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Question Text</label>
                            <input type="text" name="question_text" class="form-control"
                                placeholder="Enter question text" value="{{ old('question_text') }}" required>
                        </div>

                        {{-- الدرجة --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mark</label>
                            <input type="number" name="mark" class="form-control" placeholder="Enter mark"
                                step="0.01" value="{{ old('mark') }}" min="0" required>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('questions.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-check2-circle"></i> Save question
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
