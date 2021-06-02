<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Sponsor
 * @package Happy\Lotto\Models
 * @property $name 赞助商名称
 * @property $icon_url 赞助商icon
 * @property $app_id 赞助商小程序APP_ID
 * @property $path_url 跳转下的url
 * @property $content 小程序介绍内容
 * @property $share_text 分享文案
 * @property $status 是否开启 1表示开启，0表示关闭
 * @property $self_support 是否自营
 * @property $description 赞助商介绍
 */
class Sponsor extends Model
{
    protected $table = 'lotto_sponsor';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'icon_url', 'app_id', 'path_url', 'content', 'share_text', 'status',
        'self_support', 'description', 'create_time', 'update_time', 'delete_time'
    ];

}
