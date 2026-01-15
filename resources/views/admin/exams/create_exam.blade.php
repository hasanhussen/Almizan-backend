@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-danger"> Create New Exam</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('exams.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        {{-- المادة --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subject</label>
                            <select name="subject_id" id="subjectSelect" class="form-select" required disabled>
                                <option value="" disabled selected>Select subject</option>
                            </select>

                        </div>

                        {{-- السنة الدراسية --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Year</label>
                            <select name="year" id="yearSelect" class="form-select" required>
                                <option value="" disabled selected>Select year</option>
                                <option value="1st">1st</option>
                                <option value="2nd">2nd</option>
                                <option value="3rd">3rd</option>
                                <option value="4th">4th</option>
                            </select>

                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Exam Term</label>

                            <div class="d-flex gap-2">
                                {{-- اسم الدورة --}}
                                <select name="term_name" class="form-select" required>
                                    <option value="" disabled selected>الدورة</option>
                                    <option value="First Semester"
                                        {{ old('term_name') == 'First Semester' ? 'selected' : '' }}>
                                        First Semester
                                    </option>
                                    <option value="Second Semester"
                                        {{ old('term_name') == 'Second Semester' ? 'selected' : '' }}>
                                        Second Semester
                                    </option>
                                    <option value="Supplementary"
                                        {{ old('term_name') == 'Supplementary' ? 'selected' : '' }}>
                                        Supplementary
                                    </option>
                                </select>

                                {{-- الموسم الدراسي (صغير مثل country code) --}}
                                <select name="season" class="form-select w-auto" required>
                                    @php
                                        $current = date('Y');
                                        $seasons = [
                                            $current - 1 . '-' . $current,
                                            $current . '-' . ($current + 1),
                                            $current + 1 . '-' . ($current + 2),
                                        ];
                                    @endphp

                                    @foreach ($seasons as $season)
                                        <option value="{{ $season }}"
                                            {{ old('season') == $season ? 'selected' : '' }}>
                                            {{ $season }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Exam Date</label>
                            <input type="date" name="exam_date" class="form-control" value="{{ old('exam_date') }}"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Time</label>
                            <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}"
                                required>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Actual Duration</label>
                            <input type="number" name="actual_duration" class="form-control"
                                placeholder="Enter actual duration (in minutes)" value="{{ old('actual_duration') }}"
                                required min="1">
                        </div>

                        {{-- نوع الامتحان --}}
                        <div class="col-md-6">
                            <label for="exam_type" class="form-label">Exam Type</label>
                            <select name="exam_type" class="form-select" required>
                                <option value="" disabled selected>Select exam type</option>
                                <option value="quiz" {{ old('exam_type') == 'quiz' ? 'selected' : '' }}>اختبار قصير / Quiz
                                </option>
                                <option value="midterm" {{ old('exam_type') == 'midterm' ? 'selected' : '' }}>امتحان نصف
                                    الفصل / Midterm</option>
                                <option value="final" {{ old('exam_type') == 'final' ? 'selected' : '' }}>امتحان نهائي /
                                    Final Exam</option>
                                <option value="assignment" {{ old('exam_type') == 'assignment' ? 'selected' : '' }}>واجب /
                                    Assignment</option>
                                <option value="project" {{ old('exam_type') == 'project' ? 'selected' : '' }}>مشروع / Project
                                </option>
                                <option value="participation" {{ old('exam_type') == 'participation' ? 'selected' : '' }}>
                                    مشاركة / Participation</option>
                                <option value="oral" {{ old('exam_type') == 'oral' ? 'selected' : '' }}>امتحان شفوي / Oral
                                    Exam</option>
                                <option value="practice" {{ old('exam_type') == 'practice' ? 'selected' : '' }}>امتحان تدريبي
                                    / Practice Exam</option>
                                <option value="makeup" {{ old('exam_type') == 'makeup' ? 'selected' : '' }}>امتحان تعويضي /
                                    Makeup Exam</option>
                            </select>

                        </div>


                        {{-- الدرجة الكلية --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Marks</label>
                            <input type="number" name="total_marks" class="form-control" placeholder="Enter total marks"
                                value="{{ old('total_marks') }}" required min="1">
                        </div>

                        {{-- نسبة النجاح --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Success Rate</label>
                            <input type="number" name="success_rate" class="form-control" placeholder="Enter success rate"
                                value="{{ old('success_rate') }}" required min="0" step="0.5">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('exams.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check2-circle"></i> Save exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.getElementById('yearSelect').addEventListener('change', function() {
            let year = this.value;
            let subjectSelect = document.getElementById('subjectSelect');

            subjectSelect.innerHTML = '<option disabled selected>Loading...</option>';
            subjectSelect.disabled = true;

            fetch(`/admin/subjects/by-year/${year}`)
                .then(response => response.json())
                .then(data => {
                    subjectSelect.innerHTML = '<option value="" disabled selected>Select subject</option>';

                    data.forEach(subject => {
                        subjectSelect.innerHTML += `
                    <option value="${subject.id}">
                        ${subject.name}
                    </option>
                `;
                    });

                    subjectSelect.disabled = false;
                });
        });
    </script>
@endsection
