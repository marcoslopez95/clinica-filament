<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory, SoftDeletes;

    public function typeDocument(): BelongsTo
    {
        return $this->belongsTo(TypeDocument::class, 'type_document_id');
    }
}
