<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $client
 * @property int $gateway
 * @property string $external_id
 * @property string $status
 * @property int $amount
 * @property string $last_number
 */
class Transaction extends Model
{

    protected $table = 'transactions';

    protected $fillable = [
        'client',
        'gateway',
        'external_id',
        'status',
        'amount',
        'card_last_numbers',
    ];

}