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
                        <h6 class="mb-0 text-white">تعديل بيانات المستخدم</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            {{-- الاسم --}}
                            <div class="mb-3">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- الايميل --}}
                            <div class="mb-3">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            @if (auth()->user()->hasRole('admin'))
                                {{-- الدور --}}
                                <div class="mb-3">
                                    <label class="form-label">الدور</label>
                                    <select name="role" id="roleSelect"
                                        class="form-select @error('role') is-invalid @enderror">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- صورة المستخدم --}}
                                <div class="mb-3" id="studentImageWrapper">
                                    <label class="form-label">صورة المستخدم</label>

                                    <div class="d-flex align-items-center gap-3">
                                        <!-- الدائرة اللي تعرض الصورة الحالية أو الجديدة -->
                                        <div id="imagePreview"
                                            style="width: 70px; height: 70px; border-radius: 50%; overflow:hidden; background:#f0f0f0; border:2px solid #ddd;">
                                            <img id="previewImg"
                                                src="{{ $user->image ? asset('storage/' . $user->image) : asset('assets/img/default-user.png') }}"
                                                alt="User Image" style="width:100%; height:100%; object-fit:cover;">
                                        </div>

                                        <!-- رفع صورة جديدة -->
                                        <input type="file" name="image" accept="image/*" id="image"
                                            class="form-control @error('image') is-invalid @enderror">
                                    </div>

                                    <small class="text-muted d-block mt-1">
                                        اترك الحقل فارغًا إذا لا تريد تغيير الصورة
                                    </small>

                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- سنة الطالب --}}
                                <div class="mb-3" id="studentYearWrapper">
                                    <label class="form-label">سنة الطالب</label>
                                    <select name="year" id="year"
                                        class="form-select @error('year') is-invalid @enderror">
                                        <option value="">اختر السنة</option>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ old('year', $user->year) == $year ? 'selected' : '' }}>
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
                                    <label class="form-label">كلمة المرور (اختياري)</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="اتركها فارغة إذا لا تريد التغيير">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- تأكيد كلمة المرور --}}
                                <div class="mb-3">
                                    <label class="form-label">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="أعد كتابة كلمة المرور">
                                </div>
                            @endif

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    حفظ التعديلات
                                </button>
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
        const previewImg = document.getElementById('previewImg');

        const yearWrapper = document.getElementById('studentYearWrapper');
        const yearSelect = document.getElementById('year');

        function toggleStudentFields() {
            if (roleSelect.value === 'student') {
                // الصورة
                imageWrapper.classList.remove('d-none');
                imageInput.removeAttribute('required'); // بالتعديل ليست إلزامية

                // السنة
                yearWrapper.classList.remove('d-none');
                yearSelect.setAttribute('required', 'required');
            } else {
                // الصورة
                imageWrapper.classList.add('d-none');
                imageInput.value = '';

                // السنة
                yearWrapper.classList.add('d-none');
                yearSelect.removeAttribute('required');
                yearSelect.value = '';
            }
        }

        // معاينة الصورة الجديدة
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        roleSelect.addEventListener('change', toggleStudentFields);

        // عند تحميل الصفحة (حتى مع validation error)
        toggleStudentFields();
    </script>
@endsection
