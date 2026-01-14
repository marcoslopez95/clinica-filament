<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_service_details')
            ->withPivot('quantity');
    }

    public function productDetails(): HasMany
    {
        return $this->hasMany(ProductServiceDetail::class);
    }
}
