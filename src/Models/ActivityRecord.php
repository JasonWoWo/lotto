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
    const STATUS_NOT_HIT = 0;
    const STATUS_SELECTED = 1;

    protected $table = 'lotto_participate_record';

    public $timestamps = false;

    public static $awardStatusMapping = [
        self::STATUS_NOT_HIT => '未中奖',
        self::STATUS_SELECTED => '中奖'
    ];

    protected $primaryKey = 'id';

    protected $fillable = [
        'lotto_activity_id', 'uid', 'cost_score', 'status', 'create_time'
    ];
}
