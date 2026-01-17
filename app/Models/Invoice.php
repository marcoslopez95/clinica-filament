<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Enums\InvoiceType;
use App\Enums\InvoiceStatus;
use App\Models\Currency;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Services\InvoiceStatusService;

class Invoice extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;

    /* -----------------------------------------------------------------
     |  Casts
     | ----------------------------------------------------------------- */
    protected function casts(): array
    {
        return [
            'total'        => 'float',
            'exchange'     => 'float',
            'date'         => 'date',
            'status'       => InvoiceStatus::class,
            'invoice_type' => InvoiceType::class,
            'is_expired'   => 'boolean',
            'is_quotation' => 'boolean',
        ];
    }

    /* -----------------------------------------------------------------
     |  Accessors / Attributes
     | ----------------------------------------------------------------- */
    public function totalPaid(): Attribute
    {
        $get = function () {
            $paymentsTotal = $this->payments->sum(function ($item) {
                $exchange   = (float) ($item->exchange ?? 1);
                $amount     = (float) ($item->amount ?? 0);
                $currencyId = (int) ($item->currency_id ?? 0);

                if ($exchange <= 0) $exchange = 1;

                return $currencyId === 1 ? $amount : $amount / $exchange;
            });

                $refundsTotal = $this->refunds()
                ->get()
                ->sum(function ($item) {
                    $exchange   = (float) ($item->exchange ?? 1);
                    $amount     = (float) ($item->amount ?? 0);
                    $currencyId = (int) ($item->currency_id ?? 0);

                    if ($exchange <= 0) $exchange = 1;

                    return $currencyId === 1 ? $amount : $amount / $exchange;
                });

            return $paymentsTotal - $refundsTotal;
        };

        return new Attribute($get);
    }

    public function balance(): Attribute
    {
        return new Attribute(fn () => $this->total - $this->total_paid);
    }

    public function toPayWithDiscounts(): Attribute
    {
        return new Attribute(fn () => $this->balance - $this->discounts->sum('amount'));
    }

    /* -----------------------------------------------------------------
     |  Business Logic
     | ----------------------------------------------------------------- */
    public function calculateSimpleBalance(): float
    {
        return (float) $this->total_paid - (float) $this->total;
    }

    public function calculateBalanceWithDiscounts(): float
    {
        $totalPaid      = (float) $this->total_paid;
        $totalDiscounts = (float) $this->discounts->sum('amount');

        return ($totalPaid + $totalDiscounts) - (float) $this->total;
    }

    public function isComplete(): bool
    {
        $total = (float) $this->total;

        if ($total <= 0) {
            return false;
        }

        if ($this->invoice_type === InvoiceType::INVENTORY) {
            return $this->calculateBalanceWithDiscounts() >= -0.01;
        }

        return $this->calculateSimpleBalance() >= -0.01;
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | ----------------------------------------------------------------- */
    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function typeDocument(): BelongsTo
    {
        return $this->belongsTo(TypeDocument::class, 'type_document_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(InvoiceDiscount::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function inventories()
    {
        $relation = $this->hasManyThrough(
            Inventory::class,
            InvoiceDetail::class,
            'invoice_id',   // Foreign key on InvoiceDetail referencing Invoice
            'product_id',   // Foreign key on Inventory referencing Product
            'id',           // Local key on Invoice
            'content_id'    // Local key on InvoiceDetail that stores the product id
        );

        return $relation->where((new InvoiceDetail())->getTable() . '.content_type', Product::class);
    }
}
