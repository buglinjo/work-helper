<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkConfig extends Model
{
    protected $table = 'work_config';

    protected $fillable = [
        'id',
        'user_id',
        'timezone',
        'start_date',
        'work_day_starts',
        'work_day_ends',
        'lunch_break_starts',
        'lunch_break_ends',
        'num_of_workdays',
        'pay_frequency_id',
        'hourly_wage',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function payFrequency(){
        return $this->belongsTo('App\PayFrequency', 'pay_frequency_id', 'id');
    }
}
