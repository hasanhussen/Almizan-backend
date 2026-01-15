<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\EditUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use App\Traits\HasImageUpload;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserNotification;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;


class UserController extends Controller
{

    use AuthorizesRequests, HasImageUpload;


    public function create()
    {
        $hideSearch = true;
        $roles = [];
        $years = ['1st', '2nd', '3rd', '4th'];

        if (auth()->user()->hasRole('admin')) {
            $roles = Role::all();
        }

        return view('admin.users.create_user', compact('roles', 'hideSearch', 'years'));
    }



    public function store(RegisterRequest $request)
    {
        $authUser = auth()->user();

        $requestedRole = $request->input('role', 'student');

        $this->authorize('create', [User::class, $requestedRole]);

        // الدور النهائي
        $roleName = $authUser->hasRole('admin')
            ? $requestedRole
            : 'student';

        // إنشاء المستخدم
        if ($request->hasFile('image')) {
            $user = $this->handleImageCreation($request->validated(), User::class, 'students');
        } else {
            $user = User::create($request->validated());
        }

        // تعيين الدور
        $user->assignRole($roleName);

        // التحقق من الإيميل
        if ($roleName === 'student') {
            event(new Registered($user));
        } else {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User added successfully.');
    }


    public function updateProfile(EditUserRequest $request, User $user)
    {
        $authUser = auth()->user();
        $requestedRole = $request->input('role', 'student');

        // Authorization
        $this->authorize('update', $user);

        $data = $request->validated();

        // Handle password
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        // Handle image
        if ($request->hasFile('image')) {
            $this->handleImageUpdate($data, $user, 'profile_images');
        } else {
            $user->update($data);
        }

        // Assign role safely
        $roleName = $authUser->hasRole('admin') ? $requestedRole : 'student';
        $user->syncRoles($roleName);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }



    public function edit(User $user)
    {

        $hideSearch = true;
        $roles = [];
        $years = ['1st', '2nd', '3rd', '4th'];

        if (auth()->user()->hasRole('admin')) {
            $roles = Role::all();
        }
        return view('admin.users.edit_user', compact('user', 'roles', 'hideSearch', 'years'));
    }





    public function login(LoginRequest $request)
    {

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
            ])->withInput();
        }

        $userData = User::where('email', $request->email)->firstOrFail();
        Auth::login($userData);
        return redirect()->route('home')->with('success', 'تم تسجيل الدخول بنجاح');
    }


    public function index(Request $request)
    {
        // ======================
        // جدول الطلاب
        // ======================
        $studentsQuery = User::role('student');

        // البحث بالاسم أو الايميل
        if ($request->filled('search')) {
            $search = $request->search;
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        // فلترة حسب حالة الحرمان
        if ($request->filled('revoked_statuses')) {
            $revokedStatuses = $request->revoked_statuses; // array
            $studentsQuery->whereIn('revoked_status', $revokedStatuses);
        }

        if ($request->filled('years')) {
            $studentsQuery->whereIn('year', $request->years);
        }

        $students = $studentsQuery->paginate(10, ['*'], 'students_page')->withQueryString();

        $staffs = collect(); // قيمة افتراضية لو مش admin

        if (auth()->user()->hasRole('admin')) {
            $staffQuery = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'supervisor', 'teacher']);
            });

            // فلترة حسب الدور (checkboxes)
            if ($request->filled('roles')) {
                $roles = $request->roles; // array of role IDs
                $staffQuery->whereHas('roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                });
            }

            // البحث بالاسم أو الايميل
            if ($request->filled('search')) {
                $search = $request->search;
                $staffQuery->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%");
                });
            }

            $staffs = $staffQuery->paginate(10, ['*'], 'staff_page')->withQueryString();
        }

        $roles = Role::all(); // للفلترة بالجدول الثاني

        return view('admin.users.users', compact('students', 'staffs', 'roles'));
    }





    public function show(User $user)
    {
        abort_if(!$user->hasRole('student'), 404);

        $hideSearch = true;
        return view('admin.users.show_student', [
            'student' => $user,
            'hideSearch' => $hideSearch
        ]);
    }



    public function ban(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->revoked_status == '2') {
            // Unban
            $user->revoked_status = '1';
            $user->revoked_reason = null;
            $user->revoked_until = null;
            $user->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم إلغاء الحرمان']);
            }
            return back()->with('success', 'تم إلغاء الحرمان');
        } else {
            if ($request->revoked_reason) {
                $days = (int)$request->input('revoked_until');
                $user->revoked_reason = $request->revoked_reason;
                $user->revoked_until = Carbon::now()->addDays($days);
            } elseif ($request->quick_reason) {
                $user->revoked_reason = $request->quick_reason;
                $user->revoked_until = $request->revoked_until ?? null;
            } else {
                $user->revoked_reason = "لا يوجد سبب محدد";
                $user->revoked_until = $request->revoked_until ?? null;
            }

            $user->revoked_status = '2';
            $user->revoked_count += 1;
            $user->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم حرمان الطالب ']);
            }
            return back()->with('success', 'تم حرمان الطالب ');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $auth = Auth::user();
        if ($user->hasRole('admin')) {
            if ($auth->created_at == $user->created_at) {
                $adminsCount = User::role('admin')->count();

                if ($adminsCount <= 1) {
                    return response()->json(['success' => false, 'message' => 'يجب أن يكون هناك مدير واحد على الأقل.'], 403);
                    // return back()->with('error', '❌  يجب أن يكون هناك مدير واحد على الأقل.');
                }
            }
            if ($auth->created_at > $user->created_at) {
                return response()->json(['success' => false, 'message' => 'لا يمكنك حذف مدير أقدم منك.'], 403);
                // return back()->with('error', '❌ لا يمكنك حذف مدير أقدم منك.');
            }
        }
        $user->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف المستخدم بنجاح.']);

        // return back()->with('success', 'تم حذف المستخدم بنجاح.');
    }



    public function updateUserRole(Request $request, $id)
    {
        $auth = Auth::user();       // الأدمن المتصل
        $user = User::findOrFail($id);


        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $newRole = $request->role;


        if ($auth->id == $user->id) {
            $admins = User::role(['admin'])->get();
            if ($admins <= 1 && !$newRole === 'admin') {
                return back()->with('error', '❌  يجب أن يكون هناك مدير واحد على الأقل.');
            }
        }


        if ($user->hasRole('admin') && $auth->created_at > $user->created_at) {
            return back()->with('error', '❌ لا يمكنك تعديل دور مدير أقدم منك.');
        }



        $user->syncRoles([$newRole]);


        return back()->with('success', '✔ تم تحديث دور المستخدم بنجاح.');
    }


    public function promote(Request $request)
    {
        $carryLimit = (int) $request->carry_limit;
        $subjectsPerYear = 12;

        $students = User::whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->whereNotIn('year', ['graduate'])
            ->get();

        foreach ($students as $student) {

            $passed = $student->subject_success;

            switch ($student->year) {
                case '1st':
                    if ($passed >= ($subjectsPerYear - $carryLimit)) {
                        $student->year = '2nd';
                    }
                    break;

                case '2nd':
                    if ($passed >= ($subjectsPerYear * 2 - $carryLimit)) {
                        $student->year = '3rd';
                    }
                    break;

                case '3rd':
                    if ($passed >= ($subjectsPerYear * 3 - $carryLimit)) {
                        $student->year = '4th';
                    }
                    break;

                case '4th':
                    if ($passed >= ($subjectsPerYear * 4)) {
                        $student->year = 'graduate';
                    }
                    break;
            }

            $student->save();
        }

        return back()->with('success', ' تم تنفيذ الترفيع بنجاح');
    }



    // public function allCards()
    // {
    //     $year = request()->get('year'); // السنة المطلوبة من الفلترة

    //     $query = User::role('student');

    //     if($year) {
    //         $query->where('year', $year);
    //     }

    //     $students = $query->paginate(12)->withQueryString();

    //     // للحصول على كل السنوات الموجودة في قاعدة البيانات (لتعبئة قائمة الفلترة)
    //  $years = ['1st', '2nd', '3rd', '4th'];

    //     return view('admin.users.all-cards', compact('students', 'years', 'year'));
    // }


    // public function printCard($id)
    // {
    //     $student = User::findOrFail($id);

    //     $pdf = Pdf::loadView('admin.users.card-pdf', compact('student'))
    //               ->setPaper([0, 0, 330, 210]); // حجم A7 بالـ px (تقريباً 85x55 mm)

    //     return $pdf->stream("Almizan-Student-{$student->id}.pdf");
    // }


}
