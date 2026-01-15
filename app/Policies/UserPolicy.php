<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function update(User $authUser, User $targetUser): bool
    {
        // الادمن يعدل الكل
        if ($authUser->hasRole('admin')) {
            return true;
        }

        // المشرف يعدل الطلاب فقط
        if (
            $authUser->hasRole('supervisor') &&
            $targetUser->hasRole('student')
        ) {
            return true;
        }

        return false;
    }

    public function create(User $authUser, string $role): bool
    {
        if ($authUser->hasRole('admin')) {
            return true;
        }

        if (
            $authUser->hasRole('supervisor') &&
            $role === 'student'
        ) {
            return true;
        }

        return false;
    }
}
