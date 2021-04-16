<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityRecord
 * @package Happy\Lotto\Models
 * @property $lotto_activity_id 活动ID
 * @property $cost_score 消耗积分
 * @property $status 中奖状态， 0表示未中奖，1表示中奖
 */
class ActivityRecord extends Model
{
    protected $table = 'lotto_prize_record';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'lotto_activity_id', 'cost_score', 'status', 'create_time', 'update_time'
    ];
}
