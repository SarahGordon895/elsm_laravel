<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('view-users') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create-users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('edit-users') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermission('delete-users') && $user->id !== $model->id;
    }

    public function manageRoles(User $user): bool
    {
        return $user->hasPermission('manage-user-roles');
    }
}
