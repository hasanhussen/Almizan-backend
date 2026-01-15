<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Card</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        .card {
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
            position: relative;
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
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .front-info div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .role-badge {
            background: #1b263b;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            display: inline-block;
            margin-top: 5px;
        }

        .back-content {
            font-size: 0.8rem;
            text-align: center;
            margin-top: 20px;
        }

        .back-content h6 {
            margin-bottom: 5px;
        }

        .back-content p {
            margin: 2px 0;
        }
    </style>
</head>

<body>

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

</body>

</html>
