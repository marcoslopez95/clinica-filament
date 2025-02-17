<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DoctorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;

    }

    public function view(User $user, Doctor $doctor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return true;
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return true;
    }

    public function restore(User $user, Doctor $doctor): bool
    {
        return true;
    }

    public function forceDelete(User $user, Doctor $doctor): bool
    {
        return true;
    }
}
