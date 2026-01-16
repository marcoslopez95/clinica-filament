<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = ['name', 'buy_price', 'sell_price', 'unit_id', 'product_category_id', 'currency_id'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function bundleProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product', 'parent_id', 'child_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->bundleProducts();
    }

    public function parentProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product', 'child_id', 'parent_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'product_service_details')
                    ->withPivot('quantity');
    }

}
