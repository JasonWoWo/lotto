<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RewardRecord
 * @package Happy\Lotto\Models
 * @property $lotto_activity_id 活动ID
 * @property $project_id 奖项ID
 * @property $project_name 奖项名称
 * @property $reward_num 中奖数量
 * @property $prize_id 奖品ID
 * @property $prize_name 奖品名称
 * @property $username 中奖姓名
 * @property $nickname 中奖昵称
 * @property $avatar 头像
 * @property $mobile 手机号
 * @property $address 地址
 * @property $award_type 类型 1表示实物，2表示虚拟
 * @property $award_value 虚拟中奖金额
 * @property $fast_num 中奖商品快递单号信息
 * @property $fast_status 中奖商品发放状态
 */
class RewardRecord extends Model
{
    protected $table = 'lotto_reward_record';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'lotto_activity_id', 'project_id', 'project_name', 'reward_num', 'prize_id', 'prize_name',
        'username', 'nickname', 'avatar', 'mobile', 'address', 'award_value',
        'award_type', 'fast_status', 'fast_num'
    ];
}
