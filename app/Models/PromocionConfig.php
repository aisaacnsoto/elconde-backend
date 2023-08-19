<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocionConfig extends Model
{
    use HasFactory;

    protected $table = 'promociones_config';
    protected $keyType = 'string';
}
