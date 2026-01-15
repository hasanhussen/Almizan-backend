@extends('admin.layouts.master')

@section('css')
    <style>
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .student-card {
            width: 330px;
            height: 210px;
            border: 1px solid #333;
            border-radius: 8px;
            overflow: hidden;
        }

        .card-front,
        .card-back {
            width: 100%;
            height: 100%;
        }

        .card-front {
            background: #0d1b2a;
            color: #fff;
            padding: 10px;
        }

        .card-back {
            background: #f5f5f5;
            color: #111;
            padding: 10px;
        }

        .front-header {
            text-align: center;
            font-weight: bold;
            font-size: 1rem;
        }

        .front-avatar {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border: 2px solid #fff;
            border-radius: 5px;
            display: block;
            margin: 10px auto;
        }

        .front-info {
            font-size: 0.75rem;
            margin-top: 5px;
        }

        .front-info div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .role-badge {
            background: #1b263b;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            display: inline-block;
            margin-top: 5px;
        }

        .back-content {
            font-size: 0.7rem;
            text-align: center;
            margin-top: 10px;
        }

        .back-content h6 {
            margin-bottom: 5px;
        }

        .back-content p {
            margin: 2px 0;
        }
    </style>
@endsection

@section('content')
    <div class="container py-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>All Student Cards</h5>

            {{-- فلترة السنة --}}
            <form method="GET" action="{{ route('students.cards') }}" class="d-flex align-items-center gap-2">
                <select name="year" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}" {{ isset($year) && $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="cards-container">
            @foreach ($students as $student)
                <div class="student-card">
                    <div class="card-front">
                        <div class="front-header">Almizan Academy</div>
                        <img src="{{ asset('storage/' . $student->image) }}" class="front-avatar" alt="Student Image">
                        <div class="front-info">
                            <div><span>Name:</span> <strong>{{ $student->name }}</strong></div>
                            <div><span>Email:</span> <strong>{{ $student->email }}</strong></div>
                            <div><span>Year:</span> <strong>{{ $student->year }}</strong></div>
                            <div><span>ID:</span> <strong>#{{ $student->id }}</strong></div>
                        </div>
                        <div class="role-badge">{{ strtoupper($student->role) }}</div>
                    </div>
                    <div class="card-back">
                        <div class="back-content">
                            <h6>Student Card Instructions</h6>
                            <p>• This card must be carried at all times.</p>
                            <p>• Do not lend your card to others.</p>
                            <p>• Report lost cards immediately.</p>
                            <p>• For academic assistance, contact Almizan Academy front desk.</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
