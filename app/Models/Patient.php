<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    public function typeDocument(): BelongsTo
    {
        return $this->belongsTo(TypeDocument::class);
    }

    protected function casts(): array
    {
        return [
            'born_date' => 'date',
        ];
    }

    public function fullName():Attribute
    {
        $get = fn() => $this->last_name.', '.$this->first_name;
        return new Attribute($get);
    }

    public function fullDocument():Attribute
    {
        $get = fn() => $this->typeDocument->code .'-'.$this->dni;
        return new Attribute($get);
    }

    public function invoices(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }
}
