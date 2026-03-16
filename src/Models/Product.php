<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $amount
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends Model
{

    protected $table = 'products';

    protected $fillable = [
        'name',
        'amount',
    ];

}