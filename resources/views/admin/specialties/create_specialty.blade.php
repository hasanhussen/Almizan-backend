@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-danger">Create New Specialty</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                {{-- لازم نحط enctype لأن فيه رفع ملف --}}
                <form action="{{ route('specialties.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        {{-- الاسم --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Specialty Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter specialty name"
                                value="{{ old('name') }}" required>
                        </div>

                        {{-- عدد الطلاب --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student Count</label>
                            <input type="number" name="student_count" class="form-control"
                                placeholder="Enter student count" value="{{ old('student_count') }}" min="0"
                                required>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('specialties.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check2-circle"></i> Save specialty
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
