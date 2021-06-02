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
    protected $table = 'lotto_prize_config';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'prize_id', 'lotto_activity_id', 'prize_num', 'award_desc',
        'show_num', 'prize_type', 'virtual_label', 'sort', 'create_time', 'update_time', 'delete_time'
    ];
}
