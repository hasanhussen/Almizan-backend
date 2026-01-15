@extends('admin.layouts.master')

@section('css')
    <style>
        .student-card {
            max-width: 520px;
            margin: auto;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            background: #fff;
            border: 1px solid #e5e5e5;
        }

        .student-card-header {
            background: #0d1b2a;
            /* أزرق داكن أكاديمي */
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-card-header h5 {
            margin: 0;
            font-size: 1.05rem;
        }

        .student-avatar {
            width: 90px;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #fff;
            background: #fff;
        }

        .student-card-body {
            padding: 20px;
            display: flex;
            gap: 20px;
        }

        .student-info {
            flex: 1;
            font-size: 0.95rem;
        }

        .student-info div {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #eee;
        }

        .student-info span {
            font-weight: 600;
            color: #555;
        }

        .student-info strong {
            color: #111;
        }

        .role-badge {
            background: #1b263b;
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">

        <div class="student-card">

            {{-- Header --}}
            <div class="student-card-header">
                <div>
                    <h5>Almizan Academy</h5>
                    <small>Official Student Identification</small>
                </div>

                <span class="role-badge">
                    {{ strtoupper($student->role) }}
                </span>
            </div>

            {{-- Body --}}
            <div class="student-card-body">

                {{-- Image --}}
                <img src="{{ asset('storage/' . $student->image) }}" alt="Student Image" class="student-avatar">

                {{-- Info --}}
                <div class="student-info">
                    <div>
                        <span>Name</span>
                        <strong>{{ $student->name }}</strong>
                    </div>

                    <div>
                        <span>Email</span>
                        <strong>{{ $student->email }}</strong>
                    </div>

                    <div>
                        <span>Academic Year</span>
                        <strong>{{ $student->year }}</strong>
                    </div>

                    <div>
                        <span>Student ID</span>
                        <strong>#{{ $student->id }}</strong>
                    </div>

                    <div>
                        <span>Registered</span>
                        <strong>{{ $student->created_at->format('d M Y') }}</strong>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
