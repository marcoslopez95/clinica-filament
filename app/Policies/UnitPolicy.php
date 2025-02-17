<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;

    }

    public function view(User $user, Unit $unit): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Unit $unit): bool
    {
        return true;
    }

    public function delete(User $user, Unit $unit): bool
    {
        return true;
    }

    public function restore(User $user, Unit $unit): bool
    {
        return true;
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return true;
    }
}
