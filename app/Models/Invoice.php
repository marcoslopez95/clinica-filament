<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected function casts(): array
    {
        return [
            'total' => 'float',
            'date' => 'date',
            'status' => InvoiceStatus::class
        ];
    }

    public function totalPaid(): Attribute
    {
        $get = fn() => $this->payments->sum(function($item) {
            $exchange = $item['exchange'] ?? 1;
            $amount = $item['amount'] ?? 0;
            return $item['currency_id'] === 1 ? $amount : $amount/$exchange;
        });
        return new Attribute($get);
    }

    public function toPay(): Attribute
    {
        return new Attribute(fn() => $this->total - $this->total_paid);
    }

    public function isComplete(): bool
    {
        return $this->total_paid === $this->total;
    }

    protected static function booting()
    {
        static::creating(function(Invoice $invoice){
            if(!$invoice->status){
                $invoice->status = InvoiceStatus::OPEN->value;
            }
        });

        parent::booting(); // TODO: Change the autogenerated stub
    }


    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
