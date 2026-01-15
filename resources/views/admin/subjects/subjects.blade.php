@extends('admin.layouts.master')

@section('content')
    <div class="container py-3">
        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-danger">Subjects Management</h4>
            <a href="{{ route('subjects.create') }}" class="btn btn-info">
                <i class="bi bi-plus-lg"></i> Add New Subject
            </a>
        </div>




        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>

                            <th>Name</th>
                            <th class="text-center">Year</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Mark</th>
                            <th class="text-center">Success Rate</th>
                            <th class="text-center">Teachers</th>
                            @can('manage subjects')
                                <th class="text-center">Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>

                                <td style="padding-left: 10px;">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $subject->name ?? '-' }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $subject->year ?? '-' }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $subject->mark ?? '-' }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $subject->semester ?? '-' }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <span class="text-sm">
                                        {{ $subject->success_rate ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($subject->teachers->count() === 1)
                                        <span class="badge bg-secondary">
                                            {{ $subject->teachers->first()->name }}
                                        </span>
                                    @elseif($subject->teachers->count() > 1)
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#teachersModal{{ $subject->id }}">
                                            View Teachers ({{ $subject->teachers->count() }})
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>


                                @can('manage subjects')
                                    {{-- الأزرار --}}
                                    <td class="d-flex gap-1" style="justify-content: center; padding-top: 20px;">


                                        {{-- تعديل --}}
                                        <a href="{{ route('subjects.edit', $subject->id) }}"
                                            class="btn btn-sm btn-info d-flex align-items-center" title="Edit">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>

                                        {{-- حذف --}}
                                        <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST"
                                            class="d-inline delete-subject-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center"
                                                title="Delete">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>

                            <div class="modal fade" id="teachersModal{{ $subject->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                Teachers of {{ $subject->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            @foreach ($subject->teachers as $teacher)
                                                <span class="badge bg-secondary mb-1">
                                                    {{ $teacher->name }}
                                                </span>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">No subjects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-subject-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'هل أنت متأكد من الحذف؟',
                        text: "⚠️ عند حذف هذه المادة سيتم حذف جميع الامتحانات المرتبطة بها أيضًا!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، احذف!',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
