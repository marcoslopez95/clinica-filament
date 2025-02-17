<?php

namespace App\Policies;

use App\Models\TypeDocument;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TypeDocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;

    }

    public function view(User $user, TypeDocument $typeDocument): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TypeDocument $typeDocument): bool
    {
        return true;
    }

    public function delete(User $user, TypeDocument $typeDocument): bool
    {
        return true;
    }

    public function restore(User $user, TypeDocument $typeDocument): bool
    {
        return true;
    }

    public function forceDelete(User $user, TypeDocument $typeDocument): bool
    {
        return true;
    }
}
