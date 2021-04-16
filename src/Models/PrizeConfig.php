<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

class PrizeConfig extends Model
{
    protected $table = 'lottery_prize_config';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = ['prize_id', 'lottery_activity_id', 'prize_num', 'award_desc',
        'show_num', 'prize_type',
    ];
}
