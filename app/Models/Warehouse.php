<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory, SoftDeletes;

    public function inventories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public static function getBodega(): Warehouse
    {
        return self::firstOrCreate(['name' => 'Bodega'],[
            'description' => 'Almacén principal de suministros',
            'location' => 'bodega'
        ]);
    }

}
