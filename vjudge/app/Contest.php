<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    public function problems()
    {
        return $this->hasMany('App\Contest_Problem');
    }
}
