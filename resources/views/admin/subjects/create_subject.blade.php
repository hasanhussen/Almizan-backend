@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-danger">Create New Subject</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                {{-- لازم نحط enctype لأن فيه رفع ملف --}}
                <form action="{{ route('subjects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        {{-- الاسم --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subject Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter subject name"
                                value="{{ old('name') }}" required>
                        </div>

                        {{-- السنة الدراسية --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Year</label>
                            <select name="year" class="form-select" required>
                                <option value="" disabled selected>Select year</option>
                                @php
                                    $years = ['1st', '2nd', '3rd', '4th'];
                                @endphp
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- الفصل الدراسي --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="" disabled selected>Select semester</option>
                                @php
                                    $semesters = ['first', 'second'];
                                @endphp
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester }}"
                                        {{ old('semester') == $semester ? 'selected' : '' }}>{{ $semester }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- الدرجة --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mark</label>
                            <input type="number" name="mark" class="form-control" placeholder="Enter mark"
                                value="{{ old('mark') }}" min="0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Success Rate</label>
                            <input type="number" name="success_rate" class="form-control" placeholder="Enter success rate"
                                value="{{ old('success_rate') }}" min="0" required>
                        </div>
                    </div>

                    {{-- المدرسون --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Teachers</label>

                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @forelse($teachers as $teacher)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="teachers[]"
                                        value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                        {{ is_array(old('teachers')) && in_array($teacher->id, old('teachers')) ? 'checked' : '' }}>

                                    <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                        {{ $teacher->name }}
                                        <small class="text-muted">({{ $teacher->email }})</small>
                                    </label>
                                </div>
                            @empty
                                <span class="text-muted">No teachers found.</span>
                            @endforelse
                        </div>

                        <small class="text-muted">
                            Select one or more teachers responsible for this subject
                        </small>
                    </div>


                    <div class="mt-4 text-end">
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check2-circle"></i> Save subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
