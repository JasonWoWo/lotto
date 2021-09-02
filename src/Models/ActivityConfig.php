<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityConfig
 * @package Happy\Lotto\Models
 * @property $id
 * @property $prize_id 抽奖活动奖品ID
 * @property $lotto_activity_id 抽奖活动ID
 * @property $prize_num 奖品数量
 * @property $award_desc 奖项名，一等奖，二等奖
 * @property $show_num 展示数量
 * @property $prize_type 奖品类型 1表示实物，2表示虚拟
 * @property $virtual_label
 * @property $sort 优先级
 * @property $create_time 创建时间
 * @property $update_time 更新时间
 * @property $delete_time 删除时间
 */
class ActivityConfig extends Model
{
    const PRIZE_TYPE_PHYSICAL = 1;
    const PRIZE_TYPE_VIRTUAL = 2;

    const VIRTUAL_NONE = 0;
    const VIRTUAL_RED_ENVELOP = 1;
    const VIRTUAL_COUPON = 2;

    public static $prizeMapping = [
        self::PRIZE_TYPE_PHYSICAL => '实物',
        self::PRIZE_TYPE_VIRTUAL => '虚拟'
    ];

    public static $virtualMapping = [
        self::VIRTUAL_NONE => [
            'name' => '未配置',
            'label' => 'none'
        ],
        self::VIRTUAL_RED_ENVELOP => [
            'name' => '红包',
            'label' => 'red_envelop'
        ],
        self::VIRTUAL_COUPON => [
            'name' => '电子优惠券',
            'label' => 'coupon'
        ],
    ];

    protected $table = 'lotto_prize_config';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'prize_id', 'lotto_activity_id', 'prize_num', 'award_desc',
        'show_num', 'prize_type','virtual_type', 'virtual_label', 'virtual_value', 'sort', 'create_time', 'update_time', 'delete_time'
    ];
}
