<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialization extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;
}
