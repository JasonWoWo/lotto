<?php


namespace Happy\Lotto\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'lottery_activity';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = ['start_time', 'draw_time', 'create_time', 'update_time', 'delete_time', 'sponsor_id',
        'prize_id', 'cost_chance', 'join_limit', 'description', 'prize_type', 'body_prize', 'show_num', 'draw_limit',
        'sort', 'title'
    ];
}
