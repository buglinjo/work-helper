<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayFrequency extends Model
{
    protected $table = 'pay_frequencies';

    protected $fillable = ['id', 'name'];

    public $timestamps = false;

    public function workConfigs(){
        return $this->hasMany('App\WorkConfig', 'pay_frequency_id', 'id');
    }
}
