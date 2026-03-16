<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property int $is_active
 * @property int $priority
 */
class Gateway extends Model
{

    protected $table = 'gateways';

    protected $fillable = [
        'name',
        'is_active',
        'priority',
    ];

}