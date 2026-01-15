@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-danger">Edit Answers for Question</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('answers.update', $question->id) }}" method="POST">
                    @csrf
                    @method('Patch')

                    {{-- السؤال --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Question</label>
                        <input type="text" class="form-control" value="{{ $question->question_text }}" disabled>
                    </div>

                    {{-- الأجوبة --}}
                    <div id="answers-wrapper">
                        @foreach ($answers as $answer)
                            <div class="row g-3 align-items-center answer-item">
                                <div class="col-md-7">
                                    <input type="text" name="answers[{{ $answer->id }}]" class="form-control"
                                        value="{{ $answer->answer_text }}" required>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_correct[]"
                                            value="{{ $answer->id }}" {{ $answer->is_correct ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Correct Answer
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-2 text-end">
                                    <button type="button"
                                        class="btn btn-danger remove-answer {{ $loop->first ? 'd-none' : '' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- زر إضافة جواب جديد --}}
                    <div class="mt-3 mb-4">
                        <button type="button" id="add-answer" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Add Answer
                        </button>
                    </div>

                    {{-- رسالة خطأ إذا ما في أي جواب صحيح --}}
                    <div id="correct-error" class="text-danger mb-3 d-none">
                        ⚠️ Please select at least one correct answer.
                    </div>

                    {{-- أزرار الإرسال --}}
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('questions.index') }}" class="btn btn-secondary me-2">
                            Back
                        </a>
                        <button type="submit" class="btn btn-danger">
                            Save Answers
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // إضافة جواب جديد
        document.getElementById('add-answer').addEventListener('click', function() {
            const wrapper = document.getElementById('answers-wrapper');
            const item = wrapper.querySelector('.answer-item').cloneNode(true);

            // مسح القيم القديمة
            item.querySelector('input[type="text"]').value = '';
            item.querySelector('input[type="checkbox"]').checked = false;

            // إظهار زر الحذف
            item.querySelector('.remove-answer').classList.remove('d-none');

            wrapper.appendChild(item);
        });

        // حذف جواب
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-answer')) {
                e.target.closest('.answer-item').remove();
            }
        });

        // التحقق من وجود جواب صحيح واحد على الأقل
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="is_correct[]"]');
            const error = document.getElementById('correct-error');

            let hasCorrect = false;
            checkboxes.forEach(cb => {
                if (cb.checked) hasCorrect = true;
            });

            if (!hasCorrect) {
                e.preventDefault();
                error.classList.remove('d-none');
                error.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            } else {
                error.classList.add('d-none');
            }
        });
    </script>
@endpush
