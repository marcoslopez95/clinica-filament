<?php

namespace App\Policies;

use App\Models\Specialization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecializationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

        return true;
    }

    public function view(User $user, Specialization $specialization): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Specialization $specialization): bool
    {
        return true;
    }

    public function delete(User $user, Specialization $specialization): bool
    {
        return true;
    }

    public function restore(User $user, Specialization $specialization): bool
    {
        return true;
    }

    public function forceDelete(User $user, Specialization $specialization): bool
    {
        return true;
    }
}
