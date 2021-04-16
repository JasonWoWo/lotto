<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Prize
 * @package Happy\Lotto\Models
 * @property $id
 * @property $name 奖品名称
 * @property $image_url 奖品图片
 * @property $sku_id 单品ID
 * @property $sku_name 单品名称
 */
class Prize extends Model
{
    protected $table = 'lotto_prize';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = ['create_time', 'update_time', 'delete_time', 'name', 'image_url', 'sku_id', 'sku_name'];
}
