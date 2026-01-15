<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditSubjectRequest;
use App\Models\Subject;
use App\Http\Requests\SubjectRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Subject::with('teachers');

        if ($user->hasRole('teacher')) {
            $query->whereHas('teachers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }



        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%");
        }

        if ($request->filled('years')) {
            $query->whereIn('year', $request->years);
        }

        $subjects = $query->get();
        return view('admin.subjects.subjects', compact('subjects'));
    }

    public function byYear($year)
    {
        return Subject::where('year', $year)
            ->select('id', 'name')
            ->get();
    }



    public function create()
    {
        $teachers = User::role('teacher')->get();
        return view('admin.subjects.create_Subject', compact('teachers'));
    }


    public function store(SubjectRequest $request)
    {

        $subject = Subject::create($request->validated());
        if ($request->filled('teachers')) {
            $subject->teachers()->sync($request->teachers); // array من teacher_ids
        }
        return redirect()->route('subjects.index')->with('success', 'subject created successfully.');
    }


    public function edit(Subject $subject)
    {
        // $teachers = User::role('teacher')->get();
        $teachers = User::role('teacher')->get();
        $subject->load('teachers');
        return view('admin.subjects.edit_Subject', compact('subject', 'teachers'));
    }

    public function update(EditSubjectRequest $request, Subject $subject)
    {

        $subject->update($request->validated());

        if ($request->filled('teachers')) {
            $subject->teachers()->sync($request->teachers);
        } else {
            $subject->teachers()->detach(); // إذا لم يرسل شيء نزيل كل الأساتذة
        }

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }


    public function destroy(Subject $subject)
    {
        $subject->teachers()->detach();
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
