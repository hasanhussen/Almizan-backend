@extends('admin.layouts.master')

@section('content')
    <div class="container py-4">

        <h4 class="fw-bold mb-4 text-danger">معلومات التطبيق</h4>

        @include('admin.partials.alerts')

        <form method="POST" action="{{ route('admin.app-info.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">من نحن</label>
                <textarea name="text" class="form-control" rows="5" required>{{ old('text', $appInfo->text ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">الملاحظة القانونية</label>
                <textarea name="note" class="form-control" rows="5" required>{{ old('note', $appInfo->note ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">رابط Instagram</label>
                <input type="url" name="instagram" class="form-control"
                    value="{{ old('instagram', $appInfo->instagram ?? '') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">رابط Facebook</label>
                <input type="url" name="facebook" class="form-control"
                    value="{{ old('facebook', $appInfo->facebook ?? '') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">رقم whatsapp</label>
                <input type="text" name="whatsapp" class="form-control"
                    value="{{ old('whatsapp', $appInfo->whatsapp ?? '') }}">
            </div>



            <button class="btn btn-danger px-4">حفظ التعديلات</button>
        </form>
    </div>
@endsection
