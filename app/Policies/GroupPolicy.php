<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('groups.view');
    }

    public function view(User $user, Group $group): bool
    {
        return $user->hasPermission('groups.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('groups.create');
    }

    public function update(User $user, Group $group): bool
    {
        return $user->hasPermission('groups.edit');
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->hasPermission('groups.delete');
    }
}