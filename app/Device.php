<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';

    protected $fillable = ['platform', 'device_token'];
}
