<?php

namespace App\Models;

use App\Enums\InvoiceType;
use App\Enums\InvoiceStatus;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'total' => 'float',
            'exchange' => 'float',
            'date' => 'date',
            'status' => InvoiceStatus::class,
            'invoice_type' => InvoiceType::class,
            'is_expired' => 'boolean',
        ];
    }

    public function totalPaid(): Attribute
    {
        $get = fn() => $this->payments->sum(function($item) {
            $exchange = (float) ($item->exchange ?? 1);
            $amount = (float) ($item->amount ?? 0);
            $currencyId = (int) ($item->currency_id ?? 0);

            if ($exchange <= 0) $exchange = 1;

            return $currencyId === 1 ? $amount : $amount / $exchange;
        });
        return new Attribute($get);
    }

    public function balance(): Attribute
    {
        return new Attribute(fn() => $this->total - $this->total_paid);
    }

    public function toPayWithDiscounts(): Attribute
    {
        return new Attribute(fn() => $this->balance - $this->discounts->sum('amount'));
    }

    public function calculateSimpleBalance(): float
    {
        return (float) $this->total_paid - (float) $this->total;
    }

    public function calculateBalanceWithDiscounts(): float
    {
        $totalPaid = (float) $this->total_paid;
        $totalDiscounts = (float) $this->discounts->sum('amount');
        return ($totalPaid + $totalDiscounts) - (float) $this->total;
    }

    public function isComplete(): bool
    {
        $total = (float) $this->total;

        // Si no hay productos, no puede estar pagada (sería un abono/pago parcial si hay pagos)
        if ($total <= 0) {
            return false;
        }

        if ($this->invoice_type === InvoiceType::INVENTORY) {
            return $this->calculateBalanceWithDiscounts() >= -0.01;
        }

        return $this->calculateSimpleBalance() >= -0.01;
    }

    public function updateStatusIfPaid(): void
    {
        $this->refresh();

        if ($this->status === InvoiceStatus::CANCELLED) {
            return;
        }

        $total = (float) $this->total;
        $totalPaid = (float) $this->total_paid;
        $totalDiscounts = (float) $this->discounts->sum('amount');
        $hasMoney = $totalPaid > 0 || $totalDiscounts > 0;

        if ($this->isComplete()) {
            $this->update(['status' => InvoiceStatus::CLOSED]);
            return;
        }

        // Si no está completa (isComplete es false), determinamos si es PARTIAL o OPEN

        // Caso: Tiene pagos o descuentos (ya sea con total 0 o total > 0 pero insuficiente)
        if ($hasMoney) {
            $this->update(['status' => InvoiceStatus::PARTIAL]);
            return;
        }

        // Caso: No tiene pagos ni descuentos, se queda o vuelve a Por pagar
        $this->update(['status' => InvoiceStatus::OPEN]);
    }

    protected static function booting()
    {
        static::creating(function(Invoice $invoice){
            if(!$invoice->status){
                $invoice->status = InvoiceStatus::OPEN;
            }

            // Si al crear ya tiene pagos o descuentos (aunque Filament suele guardarlos después)
            // se marcará como Pago parcial si no está completa.
            // Pero como las relaciones se guardan después, esto es mayormente para seguridad.
        });

        static::created(function(Invoice $invoice){
            $invoice->updateStatusIfPaid();
        });

        static::updated(function(Invoice $invoice){
            // Evitar bucle infinito si solo se actualiza el status
            if ($invoice->isDirty('status') && count($invoice->getDirty()) === 1) {
                return;
            }
            $invoice->updateStatusIfPaid();
        });

        parent::booting();
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

    public function inventories()
    {
        return $this->hasManyThrough(
            Inventory::class,
            InvoiceDetail::class,
            'invoice_id',
            'product_id',
            'id',
            'product_id'
        );
    }
}
