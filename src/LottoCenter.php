<?php


namespace Happy\Lotto;


use Happy\Lotto\Service\LottoService;

class LottoCenter
{

    protected $lottoService = null;

    public function __construct(LottoService $service)
    {
        $this->lottoService = $service;
    }

    /**
     * TODO lucky_activity
     * 获取活动列表
     * @param int | null uid
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getActivities($uid = null, $page = 1, $limit = 10)
    {
        return $this->lottoService->fetchActivities($uid, $page, $limit);
    }

    /**
     * TODO activity_info
     * 获取活动的详情
     * @param int $activityId
     * @param int | null $uid
     * @return array
     */
    public function getActivityDetail($activityId, $uid = null)
    {
        return $this->lottoService->fetchActivityDetail($activityId, $uid);
    }

    /**
     * TODO prize_detail
     * 获取活动的详情的参与信息汇总
     * @param int $activityId
     * @param int $uid
     * @return array
     */
    public function getActivityParticipationSummary($activityId, $uid)
    {
        return $this->lottoService->getActivitySummary($activityId, $uid);
    }

    /**
     * TODO partake_prize
     * 参与抽奖活动报名, 积分扣除系统需要在外层处理
     * @param int $uid
     * @param int $activityId
     * @param int $starCount
     * @return array
     */
    public function participate($uid, $activityId, $starCount = 0)
    {
        return $this->lottoService->takePartInActivity($uid, $activityId, $starCount);
    }

    /**
     * TODO 虚拟抽奖活动的领奖动作
     * @param int $activityId
     * @param int $uid
     * @return array
     */
    public function takePrize($activityId, $uid)
    {
        return $this->lottoService->awardPrize($uid, $activityId);
    }

    /**
     * TODO my_prize_list
     * 抽奖活动的参与记录信息列表
     * @param $uid
     * @return array
     */
    public function getParticipationItems($uid)
    {
        return $this->lottoService->fetchParticipationRecord($uid);
    }

    /**
     * TODO my_prize_select
     * 抽奖活动的中奖记录信息列表
     * @param $uid
     * @param $page
     * @param $limit
     * @return array
     */
    public function getRewardItems($uid, $page = 1, $limit = 10)
    {
        return $this->lottoService->fetchRewardItems($uid, $page, $limit);
    }

    /**
     * TODO my_prize_num
     * 个人中心下 参与中奖、中奖记录、未领奖的数据统计
     * @param $uid
     * @return array
     */
    public function fetchMyLottoSummary($uid)
    {
        return $this->lottoService->fetchLottoSummary($uid);
    }
}
