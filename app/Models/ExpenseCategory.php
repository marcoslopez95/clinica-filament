<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
