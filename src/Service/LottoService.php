<?php


namespace Happy\Lotto\Service;


use Carbon\Carbon;
use Happy\Lotto\Models\Activity;
use Happy\Lotto\Models\ActivityConfig;
use Happy\Lotto\Models\ActivityRecord;
use Happy\Lotto\Models\RewardRecord;
use Happy\Lotto\Models\Sponsor;
use Illuminate\Support\Facades\Config;

class LottoService extends BaseService
{
    public function fetchActivities($uid = null, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $activityEntities = Activity::query()->where('start_time', "<", Carbon::tomorrow())
            ->where('draw_time', ">", Carbon::now())->offset($offset)->limit($limit)
            ->select('*')->orderBy('sort', 'desc')->orderBy('draw_time', 'desc')
            ->get();
        if (is_null($activityEntities)) {
            $this->data['activities'] = [];
            return $this->pipeline();
        }
        $activities = $activityEntities->toArray();
        $activityIds = [];
//        array_walk($activities, function (&$activity) use (&$activityIds) {
//            $activity = (array)$activity;
//        });
        $activityIds[] = array_column($activities, 'id');
        // 用户参与活动的Ids
        $participationActivityIds = [];
        if (!is_null($uid)) {
            $participateActivityEntities = ActivityRecord::query()->where('uid', $uid)
                ->whereIn('lotto_activity_id', $activityIds)->selectRaw('lotto_activity_id as activity_id')
                ->get();
            if (!is_null($participateActivityEntities)) {
                $participateActivities = $participateActivityEntities->toArray();
                array_walk($participateActivities, function ($participate) use (&$participationActivityIds) {
                    $participate = (array)$participate;
                    $participationActivityIds[] = $participate['activity_id'];
                });
            }
        }
        $buildActivityItems = [];
        foreach ($activities as $item) {
            $detail = [];
            $detail['id'] = $item['id'];
            $detail['title'] = $item['title'];
            $detail['start_time'] = strtotime($item['start_time']);
            $detail['draw_time'] = $item['draw_time'];
            $detail['is_participate'] = in_array($item['id'], $participationActivityIds) ? 1 : 0;
            $detail['sponsor_name'] = '';
            $sponsorEntity = Sponsor::query()->where('id', $item['sponsor_id'])->first();
            if (!is_null($sponsorEntity)) {
                $sponsor = $sponsorEntity->toArray();
                $detail['sponsor_name'] = $sponsor['name'];
            }
            $detail['image_url'] = '';
            $detail['prize_items'] = [];
            $prizeEntities = $this->getPrizeEntities($item['id']);
            if (!is_null($prizeEntities)) {
                $prizes = $prizeEntities->toArray();
                $detail['prize_items'] = $prizes;
                $firstPrize = array_shift($prizes);
                $detail['image_url'] = $firstPrize['image_url'];
            }
            $buildActivityItems[] = $detail;
        }
        $field = array_column($buildActivityItems, 'is_participate');
        array_multisort($field, SORT_ASC, $buildActivityItems);

        $this->data['activities'] = $buildActivityItems;

        return $this->pipeline();

    }

    public function fetchActivityDetail($activityId, $uid = null)
    {
        /** @var Activity $activityEntity */
        $activityEntity = Activity::query()->selectRaw('*')->where('id', $activityId)->first();
        if (is_null($activityEntity)) {
            $this->status = self::STATUS_FAIL;
            $this->message = '未获取到活动的信息';
            return $this->pipeline();
        }
        $activity = $activityEntity->toArray();
        $isParticipate = 0;
        if (!is_null($uid)) {
            $isParticipate = ActivityRecord::query()->where('lotto_activity_id', $activityId)
                ->where('uid', $uid)->exists();
        }
        $detailInfo = [
            'id' => $activity['id'],
            'start_time' => strtotime($activity['start_time']),
            'draw_time' => $activity['draw_time'],
            'prize_items' => [],
            'join_num' => $activity['join_num'],
            'show_num' => $activity['show_num'],
            'cost_score' => $activity['cost_score'],
            'is_participate' => $isParticipate,
            'image_url' => '',
            'sponsor_name' => '', // 赞助商名称
            'sponsor_status' => '', // 赞助商状态
            'ali_mini_id' => '', // 赞助商阿里小程序AppId
            'ali_mini_url' => '', // 赞助商小程序url
            'ali_mini_content' => '', // 阿里小程序介绍
            'ali_mini_turn_text' => '', // 阿里小程序跳转按钮文案介绍
            'ali_life_id' => '', // 赞助商生活号AppId
            'sponsor_detail' => '', // 赞助商介绍
            'description' => '', // 福利说明
        ];
        $detailInfo['prize_items'] = $this->getPrizeEntities($activity['id']);
        $sponsorEntity = Sponsor::query()->where('id', $activity['sponsor_id'])->first();
        if (!is_null($sponsorEntity)) {
            $sponsor = $sponsorEntity->toArray();
            $detailInfo['sponsor_name'] = $sponsor['name'];
            $detailInfo['sponsor_status'] = $sponsor['status'];
            $detailInfo['ali_mini_id'] = $sponsor['app_id'];
            $detailInfo['ali_mini_url'] = $sponsor['path_url'];
            $detailInfo['ali_mini_content'] = $sponsor['content'];
            $detailInfo['ali_mini_turn_text'] = $sponsor['share_text'];
            $detailInfo['sponsor_detail'] = $sponsor['description'];
        }
        $this->data = $detailInfo;
        return $this->pipeline();

    }

    public function getActivitySummary($activityId, $uid)
    {
        // todo 判断$uid用户是否中奖，如果未中奖则返回第一个中奖人的信息
        $builder = ActivityRecord::query()->where('lotto_activity_id', $activityId)
            ->where('status', ActivityRecord::STATUS_SELECTED);
        $builderClone = clone $builder;
        $isSelected = $builderClone->where('uid', $uid)->exists();
        $bingo = [];
        if (!$isSelected) {
            $recordEntity = $builder->orderBy('id', 'ASC')->first();
            $bingo = $recordEntity->toArray();
        }
        if (empty($bingo)) {
            $this->status = self::STATUS_FAIL;
            $this->message = '获取活动中奖详情列表失败';
            return $this->pipeline();
        }
        $prizeConfigTable = Config::get('lotto.lotto_config_table');
        $prizeTable = Config::get('lotto.lotto_prize_table');
        $rewardBuilder = RewardRecord::query()->leftJoin("$prizeConfigTable as pc", 'project_id', '=', 'pc.id')
            ->leftJoin("$prizeTable as lp", 'pc.prize_id', '=', 'lp.id')
            ->where('lotto_reward_record.lotto_activity_id', $activityId);
        if ($isSelected) {
            $rewardEntity = $rewardBuilder->where('lotto_reward_record.uid', $uid)->first();
            $bingoPrize = $rewardEntity->toArray();
        } else {
            $rewardEntity = $rewardBuilder->orderBy('lotto_reward_record.id', 'ASC')->first();
            $bingoPrize = $rewardEntity->toArray();
        }
        if (empty($bingoPrize)) {
            $this->status = self::STATUS_FAIL;
            $this->message = "未获取活动中奖奖项详情";
            return $this->pipeline();
        }

        $bingoInfo = [
            'username' => $this->getPrivacyInfo($bingoPrize['username']),
            'nickname' => $this->getPrivacyInfo($bingoPrize['nickname']),
            'address' => $isSelected ? $bingoPrize['address'] : '',
            'turn_url' => '',
            'turn_img' => '',
            'prize_name' => $bingoPrize['name'],
            'prize_url' => $bingoPrize['image_url'],
            'prize_num' => $bingoPrize['prize_num'],
            'avatar' => $bingoPrize['avatar'],
            'mobile' => $bingoPrize['mobile'],
            'fast_status' => $bingoPrize['fast_status'],
            'is_lucky' => $isSelected ? 1 : 0,
            'virtual_or_physical' => '',
            'red_amount' => 0.11
        ];

        $this->data = $bingoInfo;

        return $this->pipeline();

    }

    /**
     * 参与抽奖服务
     * @param $uid
     * @param $activityId
     * @param $starCounts
     * @return array
     */
    public function takePartInActivity($uid, $activityId, $starCounts)
    {
        $activityEntity = Activity::query()->where('id', $activityId)->first();
        if (is_null($activityEntity)) {
            $this->status = self::STATUS_FAIL;
            $this->message = '未获取到活动详情信息';
            return $this->pipeline();
        }
        $activity = $activityEntity->toArray();
        if (strtotime($activity['start_time']) > time()) {
            $this->status = self::STATUS_FAIL;
            $this->message = '活动未开始';
            return $this->pipeline();
        }
        if (strtotime($activity['draw_time']) < time()) {
            $this->status = self::STATUS_FAIL;
            $this->message = '活动已过期';
            return $this->pipeline();
        }
        $recordBuilder = ActivityRecord::query()->selectRaw('*');
        if (!array_key_exists($activity['join_limit'], Activity::$joinLimitMapping)) {
            $this->status = self::STATUS_FAIL;
            $this->message = '活动配置有误，请联系客服';
            return $this->pipeline();
        }
        if ($activity['join_limit'] == Activity::JOIN_LIMIT_ONCE) {
            $recordBuilder->where('uid', $uid)->where('lotto_activity_id', $activityId);
        }
        if ($activity['join_limit'] == Activity::JOIN_LIMIT_EVERYDAY) {
            $recordBuilder->where('uid', $uid)->where('lotto_activity_id', $activityId)
                ->where('create_time', '>=', Carbon::today());
        }
        $isJoin = $recordBuilder->exists();

        if ($isJoin) {
            $this->status = self::STATUS_FAIL;
            $this->message = '您已经参与过该活动哦～';
            return $this->pipeline();
        }

        if ($starCounts < $activity['cost_score']) {
            $this->status = self::STATUS_FAIL;
            $this->message = '积分不够哦';
            return $this->pipeline();
        }

        ActivityRecord::query()->insert([
            'uid' => $uid,
            'lotto_activity_id' => $activityId,
            'cost_score' => $activity['cost_score'],
            'create_time' => date('Y-m-d H:i:s')
        ]);
        Activity::query()->where('id', $activityId)->increment('join_num', 1);

        $this->data['remain_score'] = $starCounts - $activity['cost_score'];
        $this->data['cost_score'] = $activity['cost_score'];

        return $this->pipeline();
    }

    public function fetchParticipationRecord($uid)
    {
        $lottoActivityTable = Config::get('lotto.lotto_activity_table');
        $lottoPrizeConfigTable = Config::get('lotto.lotto_config_table');
        $lottoPrizeTable = Config::get('lotto.lotto_prize_table');
        $builder = ActivityRecord::query()->where('uid', $uid)
            ->leftJoin("$lottoActivityTable as la", 'lotto_activity_id', '=', 'la.id')
            ->leftJoin("$lottoPrizeConfigTable as lc", 'la.id', '=', 'lc.lotto_activity_id')
            ->leftJoin("$lottoPrizeTable as lp", "lc.prize_id", '=', "lp.id");
        $builder->selectRaw('la.id, la.start_time, la.draw_time, lc.prize_id, lc.show_num, lp.name as prize_name, lp.image_url,
         lc.prize_num, lc.`prize_type` AS `virtual_or_physical`')
            ->whereNull('la.delete_time')->whereNull('lc.delete_time')->orderBy('la.id', 'desc');
        $recordEntities = $builder->get();
        if (is_null($recordEntities)) {
            $this->data['items'] = [];
            return $this->pipeline();
        }
        $rebuildRecord = [];
        $recordItems = $recordEntities->toArray();
        foreach ($recordItems as $item) {
            if (!isset($rebuildRecord[$item['id']])) {
                $rebuildRecord[$item['id']] = [
                    'id' => $item['id'],
                    'start_time' => $item['start_time'],
                    'draw_time' => $item['draw_time'],
                    'image_url' => $item['image_url'],
                    'is_over' => strtotime($item['draw_time']) > time() ? 0 : 1,
                    'prize_items' => []
                ];
            }
            $rebuildRecord[$item['id']]['prize_items'][] = [
                'prize_id' => $item['prize_id'],
                'prize_num' => isset($item['show_num']) && $item['show_num'] > 0 ? $item['show_num'] : $item['prize_num'],
                'prize_name' => $item['prize_name'],
                'virtual_or_physical' => $item['virtual_or_physical']
            ];
        }

        $this->data['items'] = array_values($rebuildRecord);

        return $this->pipeline();

    }

    public function fetchRewardItems($uid, $page = 1, $limit = 10)
    {
        $lottoActivityTable = Config::get('lotto.lotto_activity_table');
        $rewardBuilder = RewardRecord::query()->where('uid', $uid)
            ->leftJoin("$lottoActivityTable as la", 'lotto_activity_id', '=', 'la.id')
            ->whereNull('la.delete_time');
        $rewardCounts = $rewardBuilder->count();
        if (empty($rewardCounts)) {
            $this->data['total_counts'] = 0;
            $this->data['items'] = [];
            return $this->pipeline();
        }
        $offset = ($page - 1) * $limit;
        $lottoPrizeTable = Config::get('lotto.lotto_prize_table');
        $rewardItems = $rewardBuilder
            ->leftJoin("$lottoPrizeTable as lp", "prize_id", '=', 'lp.id')
            ->selectRaw('la.id, fast_status, DATE_FORMAT(la.draw_time, "%m-%d") as draw_time, lp.name as prize_name')
            ->offset($offset)->limit($limit)
            ->get()->toArray();
        $this->data['total_counts'] = $rewardCounts;
        $this->data['items'] = $rewardItems;

        return $this->pipeline();
    }

    public function fetchLottoSummary($uid)
    {
        $this->data = [
            'participates' => 0,
            'bingo' => 0,
            'unclaimed' => 0
        ];
        $builder = ActivityRecord::query()->where('uid', $uid);

        $participateItems = $builder->selectRaw('uid, lotto_activity_id')->groupBy(['uid', 'lotto_activity_id'])->get();

        if (empty($participateItems)) {
            return $this->pipeline();
        }

        $bingoCount = $builder->where('status', ActivityRecord::STATUS_SELECTED)->count();

        $unclaimedCount = RewardRecord::query()->where('uid', $uid)
            ->where('fast_status', RewardRecord::DELIVERY_OFF)->count();
        $this->data['participates'] = count($participateItems);
        $this->data['bingo'] = $bingoCount;
        $this->data['unclaimed'] = $unclaimedCount;

    }

    /**
     * 虚拟红包的奖励金额
     * @param $uid
     * @param $activityId
     * @return array
     */
    public function awardPrize($uid, $activityId)
    {
        return $this->pipeline();
    }

    /**
     * @param $activityId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getPrizeEntities($activityId)
    {
        $prizeTable = Config::get('lotto.lotto_prize_table');
        return ActivityConfig::query()->leftJoin("$prizeTable as lp", 'prize_id', '=', 'lp.id')
            ->selectRaw('*')->where('lotto_activity_id', $activityId)->get();
    }

    private function getPrivacyInfo($name)
    {
        $len = mb_strlen($name, 'UTF-8');
        if ($len >= 3) {
            $str = "";
            for ($i = 1; $i < $len; $i++) {
                $str .= "*";
            }
            $name = mb_substr($name, 0, 1, 'UTF-8') . $str;
        } elseif ($len == 2) {
            $name = mb_substr($name, 0, 1, 'UTF-8') . '*';
        }
        return $name;
    }
}
