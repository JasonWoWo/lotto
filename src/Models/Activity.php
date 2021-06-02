<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Activity
 * @package Happy\Lotto\Models
 * @property $start_time 开始时间
 * @property $draw_time 开奖时间
 * @property $title 抽奖标题
 * @property $cost_score 消耗幸运星
 * @property $sponsor_id 赞助商ID
 * @property $join_limit 参与活动类型, 1表示用户只能参加一次，2表示用户每天可以参加一次
 * @property $join_num 实际参与人数,
 * @property $draw_limit 最低开奖人数
 * @property $description 抽奖活动描述
 * @property $show_num 初始参与人数
 * @property $sort 活动排序
 * @property $window_config 活动弹窗跳转
 * @property $get_prize_num 获取奖品的数量
 */
class Activity extends Model
{
    const JOIN_LIMIT_ONCE = 1;
    const JOIN_LIMIT_EVERYDAY = 2;

    protected $table = 'lotto_activity';

    public $timestamps = false;

    public static $joinLimitMapping = [
        self::JOIN_LIMIT_ONCE => '用户只能参加一次',
        self::JOIN_LIMIT_EVERYDAY => '用户每天可以参加一次'
    ];

    protected $primaryKey = 'id';

    protected $fillable = [
        'start_time', 'draw_time', 'title', 'cost_score', 'sponsor_id',
        'join_limit', 'draw_limit', 'description', 'join_num', 'show_num', 'window_config', 'get_prize_num',
        'sort', 'create_time', 'update_time', 'delete_time'
    ];
}
