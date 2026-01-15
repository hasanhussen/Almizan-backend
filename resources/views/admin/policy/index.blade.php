@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">

        <h4 class="fw-bold text-danger mb-4">سياسة الخصوصية</h4>

        @include('admin.partials.alerts')

        <form method="POST" action="{{ route('admin.policy.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">العنوان</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $policy->title ?? '') }}"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">النص</label>
                <textarea name="body" rows="8" class="form-control" required>
                    {{ old('body', $policy->body ?? '') }}
            </textarea>
            </div>

            <button class="btn btn-danger px-4">حفظ</button>
        </form>
    </div>
@endsection
