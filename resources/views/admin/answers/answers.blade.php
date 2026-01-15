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
            <h4 class="fw-bold mb-0 text-danger">Answers Management</h4>
            <a href="{{ route('answers.create') }}" class="btn btn-info">
                <i class="bi bi-plus-lg"></i> Add New Answer
            </a>
        </div>




        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>

                            <th>Question</th>
                            <th class="text-center">Answer</th>
                            <th class="text-center">Is correct</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($answers as $answer)
                            <tr>

                                <td class="wrap-text">
                                    <h6 class="mb-0 text-sm">
                                        {{ $answer->question->question_text }}
                                    </h6>
                                </td>

                                <td class="wrap-text text-center">
                                    <h6 class="mb-0 text-sm">
                                        {{ $answer->answer_text }}
                                    </h6>
                                </td>


                                <td class="text-center">
                                    <span class="text-sm">
                                        {{ $answer->is_correct }}
                                    </span>
                                </td>



                                {{-- الأزرار --}}
                                <td class="d-flex gap-1" style="justify-content: center; padding-top: 20px;">


                                    {{-- تعديل --}}
                                    <a href="{{ route('answers.edit', $answer->id) }}"
                                        class="btn btn-sm btn-info d-flex align-items-center" title="Edit">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>

                                    {{-- حذف --}}
                                    <form action="{{ route('answers.destroy', $answer->id) }}" method="POST"
                                        class="d-inline delete-answer-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" data-id="{{ $answer->id }}"
                                            class="btn btn-sm btn-danger d-flex align-items-center" title="Delete">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>

                                    </form>
                                </td>
                            </tr>



                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">No answers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $answers->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-answer-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // جلب الـ answer ID من الفورم
                    const answerId = form.querySelector('button[type="submit"]').dataset.id;

                    // أول تحذير عادي
                    Swal.fire({
                        title: 'هل أنت متأكد من الحذف؟',
                        text: "",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، احذف!',
                        cancelButtonText: 'إلغاء'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            // تحقق مع السيرفر إذا حذف الجواب رح يترك السؤال بلا جواب صحيح
                            const response = await fetch(
                                `{{ route('answers.checkDelete', ':id') }}`.replace(
                                    ':id', answerId), {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });


                            if (!response.ok) {
                                Swal.fire('Error',
                                    'Cannot check deletion. Please try again.',
                                    'error');
                                return;
                            }

                            const data = await response.json();


                            if (data.willDeleteQuestion) {
                                // تحذير إضافي قبل حذف السؤال
                                Swal.fire({
                                    title: 'تحذير!',
                                    text: 'بحال حذف هالجواب، سيتم حذف السؤال وكل الأجوبة المرتبطة فيه. هل تريد المتابعة؟',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#6c757d',
                                    confirmButtonText: 'نعم، احذف السؤال وكل الأجوبة!',
                                    cancelButtonText: 'إلغاء'
                                }).then((res) => {
                                    if (res.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            } else {
                                // حذف عادي
                                form.submit();
                            }
                        }
                    });
                });
            });
        });
    </script>
@endsection
