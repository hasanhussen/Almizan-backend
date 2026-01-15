@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-4 text-danger">Create Answers</h4>

        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('answers.store') }}" method="POST">
                    @csrf

                    {{-- اختيار السؤال --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Question</label>
                        <select name="question_id" class="form-select" required>
                            <option value="" disabled selected>Select Question</option>
                            @foreach ($questions as $question)
                                <option value="{{ $question->id }}">
                                    {{ $question->question_text }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- الأجوبة --}}
                    <div id="answers-wrapper">
                        <div class="row g-3 align-items-center answer-item">
                            <div class="col-md-7">
                                <input type="text" name="answers[]" class="form-control" placeholder="Enter answer"
                                    required>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check mt-2">
                                    <input class="form-check-input is-correct" type="checkbox">
                                    <label class="form-check-label">
                                        Correct Answer
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-danger remove-answer d-none">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="correct-error" class="text-danger mt-2 d-none">
                        ⚠️ Please select at least one correct answer.
                    </div>

                    {{-- أزرار --}}
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" id="add-answer" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Add Answer
                        </button>

                        <div>
                            <a href="{{ route('questions.index') }}" class="btn btn-secondary me-2">
                                Back
                            </a>
                            <button type="submit" class="btn btn-danger">
                                Save Answers
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function refreshIndexes() {
            document.querySelectorAll('.answer-item').forEach((item, index) => {
                const checkbox = item.querySelector('.is-correct');
                checkbox.name = `is_correct[${index}]`;
            });
        }

        document.getElementById('add-answer').addEventListener('click', function() {
            const wrapper = document.getElementById('answers-wrapper');
            const item = wrapper.querySelector('.answer-item').cloneNode(true);

            item.querySelector('input[name="answers[]"]').value = '';
            item.querySelector('.is-correct').checked = false;
            item.querySelector('.remove-answer').classList.remove('d-none');

            wrapper.appendChild(item);
            refreshIndexes();
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-answer')) {
                e.target.closest('.answer-item').remove();
                refreshIndexes();
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.is-correct');
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

        refreshIndexes();
    </script>
@endsection
