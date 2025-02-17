<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;

    }

    public function view(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function restore(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return true;
    }
}
