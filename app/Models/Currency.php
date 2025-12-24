<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Models\Exam;

class Currency extends Model
{
    use SoftDeletes;

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
