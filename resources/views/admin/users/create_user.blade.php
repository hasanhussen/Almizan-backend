@extends('admin.layouts.master')

@section('css')
    <style>
        .form-control {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.95rem;
            background: #fafafa;
            border: 1.5px solid #ddd;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 7px rgba(220, 53, 69, 0.25);
            background: #fff;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

        .form-label {
            font-weight: 600;
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid py-2">
        @include('admin.partials.alerts')
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card my-4 shadow">
                    <div class="card-header bg-gradient-dark text-white">
                        <h6 class="mb-0" style="color: white">إضافة مستخدم جديد</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- الاسم --}}
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">الاسم</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- الايميل --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (auth()->user()->hasRole('admin'))
                                {{-- الدور --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">الدور</label>
                                    <select name="role" id="roleSelect"
                                        class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="">اختر الدور</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ old('role') == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            {{-- صورة المستخدم --}}
                            <div class="mb-3 d-none" id="studentImageWrapper">
                                <label for="image" class="form-label fw-bold">صورة المستخدم</label>

                                <!-- الدائرة اللي راح تعرض الصورة -->
                                <div id="imagePreview"
                                    style="width:120px; height:120px; border-radius:50%; overflow:hidden; background:#f0f0f0; display:none; margin-bottom:10px;">
                                    <img id="previewImg" src="" alt="صورة المستخدم"
                                        style="width:100%; height:100%; object-fit:cover;">
                                </div>

                                <input type="file" name="image" id="image" accept="image/*"
                                    class="form-control @error('image') is-invalid @enderror">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- سنة الطالب --}}
                            <div class="mb-3 d-none" id="studentYearWrapper">
                                <label for="year" class="form-label fw-bold">سنة الطالب</label>
                                <select name="year" id="year"
                                    class="form-select @error('year') is-invalid @enderror">
                                    <option value="">اختر السنة</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- كلمة المرور --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">كلمة المرور</label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تأكيد كلمة المرور --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" required>
                            </div>


                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">إضافة المستخدم</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const roleSelect = document.getElementById('roleSelect');

        const imageWrapper = document.getElementById('studentImageWrapper');
        const imageInput = document.getElementById('image');
        const imagePreviewDiv = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        const yearWrapper = document.getElementById('studentYearWrapper');
        const yearSelect = document.getElementById('year');

        function toggleStudentFields() {
            if (roleSelect.value === 'student') {
                // الصورة
                imageWrapper.classList.remove('d-none');
                imageInput.setAttribute('required', 'required');

                // السنة
                yearWrapper.classList.remove('d-none');
                yearSelect.setAttribute('required', 'required');
            } else {
                // الصورة
                imageWrapper.classList.add('d-none');
                imageInput.removeAttribute('required');
                imageInput.value = '';
                imagePreviewDiv.style.display = 'none';
                previewImg.src = '';

                // السنة
                yearWrapper.classList.add('d-none');
                yearSelect.removeAttribute('required');
                yearSelect.value = '';
            }
        }

        // عرض الصورة عند اختيارها
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    imagePreviewDiv.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreviewDiv.style.display = 'none';
                previewImg.src = '';
            }
        });

        roleSelect.addEventListener('change', toggleStudentFields);

        // عند إعادة تحميل الصفحة (في حال validation error)
        toggleStudentFields();
    </script>
@endsection
