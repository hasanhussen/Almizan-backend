@extends('admin.layouts.master')
@section('css')
    <style>
        .wrap-text {
            white-space: normal !important;
            /* يسمح بالنزول لسطر جديد */
            word-break: break-word;
            /* يكسر الكلمات الطويلة */
            overflow-wrap: anywhere;
            /* دعم أقوى للنص الطويل */
            max-width: 350px;
            /* اختياري لتحديد عرض الخلية */
        }
    </style>
@endsection
@section('content')
    <div class="container py-3">
        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-danger">Questions Management</h4>
            <a href="{{ route('questions.create') }}" class="btn btn-info">
                <i class="bi bi-plus-lg"></i> Add New Question
            </a>
        </div>




        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>

                            <th>Exam Name</th>
                            <th class="text-center">Question</th>
                            <th class="text-center">mark</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>

                                <td style="padding-left: 10px;">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $question->exam->subject->name ?? '-' }}
                                    </h6>
                                </td>

                                <td class="wrap-text text-center">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $question->question_text ?? '-' }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <span class="text-sm">
                                        {{ $question->mark ?? '-' }}
                                    </span>
                                </td>



                                {{-- الأزرار --}}
                                <td class="d-flex gap-1" style="justify-content: center; padding-top: 20px;">


                                    {{-- تعديل --}}
                                    <a href="{{ route('questions.edit', $question->id) }}"
                                        class="btn btn-sm btn-info d-flex align-items-center" title="Edit">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>

                                    {{-- حذف --}}
                                    <form action="{{ route('questions.destroy', $question->id) }}" method="POST"
                                        class="d-inline delete-question-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center"
                                            title="Delete">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>



                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">No questions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $questions->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-question-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'هل أنت متأكد من الحذف؟',
                        text: "⚠️ عند حذف هذه المادة سيتم حذف جميع الأجوبة المرتبطة بها أيضًا!",
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
