<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 */
class Client extends Model
{

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
    ];

}