<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    public function emails()
    {
        return $this->hasMany(Email::class);
    }
}
