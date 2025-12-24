<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceValue extends Model
{
    use SoftDeletes;

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
