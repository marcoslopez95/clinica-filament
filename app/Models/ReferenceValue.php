<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ReferenceValueResult;

class ReferenceValue extends Model implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = ['exam_id', 'name', 'min_value', 'max_value'];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ReferenceValueResult::class);
    }
}
