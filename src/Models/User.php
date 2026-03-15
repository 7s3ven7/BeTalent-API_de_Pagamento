<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'role'
    ];

}