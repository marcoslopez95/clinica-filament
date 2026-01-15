<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ReferenceValueResult;

class ReferenceValue extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = ['exam_id', 'name', 'min_value', 'max_value', 'unit_id'];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ReferenceValueResult::class);
    }
}
