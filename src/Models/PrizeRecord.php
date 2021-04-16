<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

class PrizeRecord extends Model
{
    protected $table = 'lottery_prize_record';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = ['uid', 'mobile', 'prize_name', 'sponsor_id', 'fast_num',
        'fast_status', 'award_type'
    ];
}
