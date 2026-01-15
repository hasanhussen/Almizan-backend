@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-danger"> Edit Exam</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('exams.update', $exam->id) }}" method="POST">
                    @csrf
                    @method('Patch')

                    <div class="row g-3">

                        {{-- المادة --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subject</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="" disabled>Select subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- السنة --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Year</label>
                            <select name="year" class="form-select" required>
                                @php
                                    $years = ['1st', '2nd', '3rd', '4th'];
                                @endphp
                                @foreach ($years as $year)
                                    <option value="{{ $year }}"
                                        {{ old('year', $exam->year) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Exam Term --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Exam Term</label>

                            <div class="d-flex gap-2">
                                {{-- اسم الدورة --}}
                                <select name="term_name" class="form-select" required>
                                    <option value="" disabled>Select term</option>

                                    <option value="First Semester"
                                        {{ old('term_name', $termName) == 'First Semester' ? 'selected' : '' }}>
                                        First Semester
                                    </option>

                                    <option value="Second Semester"
                                        {{ old('term_name', $termName) == 'Second Semester' ? 'selected' : '' }}>
                                        Second Semester
                                    </option>

                                    <option value="Supplementary"
                                        {{ old('term_name', $termName) == 'Supplementary' ? 'selected' : '' }}>
                                        Supplementary
                                    </option>
                                </select>

                                {{-- الموسم (صغير مثل country code) --}}
                                <select name="season" class="form-select w-auto" required>
                                    @php
                                        $current = date('Y');
                                        $seasons = [
                                            $current - 1 . '-' . $current,
                                            $current . '-' . ($current + 1),
                                            $current + 1 . '-' . ($current + 2),
                                        ];
                                    @endphp

                                    @foreach ($seasons as $s)
                                        <option value="{{ $s }}"
                                            {{ old('season', $season) == $s ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- التاريخ --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Exam Date</label>
                            <input type="date" name="exam_date" class="form-control"
                                value="{{ old('exam_date', $exam->exam_date) }}" required>
                        </div>
                        @php
                            $startTime = $exam->start_time
                                ? \Carbon\Carbon::createFromFormat('H:i:s', $exam->start_time)->format('H:i')
                                : '';
                            $endTime = $exam->end_time
                                ? \Carbon\Carbon::createFromFormat('H:i:s', $exam->end_time)->format('H:i')
                                : '';
                        @endphp

                        {{-- وقت البداية --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Time</label>
                            <input type="time" name="start_time" class="form-control"
                                value="{{ old('start_time', $startTime) }}" required>

                        </div>


                        {{-- وقت النهاية --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Time</label>
                            <input type="time" name="end_time" class="form-control"
                                value="{{ old('end_time', $endTime) }}" required>


                        </div>

                        {{-- المدة الفعلية --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Actual Duration</label>
                            <input type="number" name="actual_duration" class="form-control"
                                value="{{ old('actual_duration', $exam->actual_duration) }}" min="1" required>
                        </div>

                        {{-- نوع الامتحان --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Exam Type</label>
                            {{-- <input type="text"
                               name="exam_type"
                               class="form-control"
                               value="{{ old('exam_type', $exam->exam_type) }}"
                               required><label for="exam_type" class="form-label">Exam Type</label> --}}
                            <select name="exam_type" class="form-select" required>
                                <option value="" disabled selected>Select exam type</option>
                                <option value="quiz" {{ old('exam_type', $exam->exam_type) == 'quiz' ? 'selected' : '' }}>
                                    اختبار قصير / Quiz</option>
                                <option value="midterm"
                                    {{ old('exam_type', $exam->exam_type) == 'midterm' ? 'selected' : '' }}>امتحان نصف الفصل
                                    / Midterm</option>
                                <option value="final" {{ old('exam_type', $exam->exam_type) == 'final' ? 'selected' : '' }}>
                                    امتحان نهائي / Final Exam</option>
                                <option value="assignment"
                                    {{ old('exam_type', $exam->exam_type) == 'assignment' ? 'selected' : '' }}>واجب /
                                    Assignment</option>
                                <option value="project"
                                    {{ old('exam_type', $exam->exam_type) == 'project' ? 'selected' : '' }}>مشروع / Project
                                </option>
                                <option value="participation"
                                    {{ old('exam_type', $exam->exam_type) == 'participation' ? 'selected' : '' }}>مشاركة /
                                    Participation</option>
                                <option value="oral" {{ old('exam_type', $exam->exam_type) == 'oral' ? 'selected' : '' }}>
                                    امتحان شفوي / Oral Exam</option>
                                <option value="practice"
                                    {{ old('exam_type', $exam->exam_type) == 'practice' ? 'selected' : '' }}>امتحان تدريبي /
                                    Practice Exam</option>
                                <option value="makeup"
                                    {{ old('exam_type', $exam->exam_type) == 'makeup' ? 'selected' : '' }}>امتحان تعويضي /
                                    Makeup Exam</option>
                            </select>

                        </div>

                        {{-- الدرجة الكلية --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Marks</label>
                            <input type="number" name="total_marks" class="form-control"
                                value="{{ old('total_marks', $exam->total_marks) }}" min="1" required>
                        </div>

                        {{-- نسبة النجاح --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Success Rate</label>
                            <input type="number" name="success_rate" class="form-control"
                                value="{{ old('success_rate', $exam->success_rate) }}" min="0" step="0.5"
                                required>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('exams.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check2-circle"></i> Update Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
