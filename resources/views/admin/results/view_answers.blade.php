@extends('admin.layouts.master')

@section('content')
    <div class="container py-3">
        {{-- رسائل النجاح والتحذير --}}
        @include('admin.partials.alerts')


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-danger">userAnswers Management</h4>
        </div>




        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Student</th>
                            <th class="text-center">Question</th>
                            <th class="text-center">Answer</th>
                            <th class="text-center">Is correct</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userAnswers as $userAnswer)
                            <tr>

                                <td class="text-center">
                                    {{ $user->name ?? '-' }}
                                </td>

                                <td class="text-center" style="padding-left: 10px;">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $userAnswer->question->question_text }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <h6 class="mb-0 text-sm" style="margin-left: 10px;">
                                        {{ $userAnswer->answer->answer_text }}
                                    </h6>
                                </td>

                                <td class="text-center">
                                    <span class="text-sm">
                                        {{ $userAnswer->answer->is_correct }}
                                    </span>
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
            {{ $userAnswers->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
