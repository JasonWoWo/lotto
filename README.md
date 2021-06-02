### 1.包安装


### 2.
####2.1 execute composer 

```bash
composer require "olaf/lotto": "dev-master"
```
#### 2.2 Provider配置文件

2.2.1 Open your `config/app.php` and add the following to the `providers` array:
 
```bash
Happy\Lotto\ServiceProvider\LottoServiceProvider::class
```

2.2.2 Run the command below to publish the package config file `config/lotto.php`

```bash
php artisan vendor:publish --provider="Happy\Lotto\ServiceProvider\LottoServiceProvider"
```

### 3.数据库设计

```bash

CREATE TABLE `lotto_activity` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `start_time` datetime NOT NULL COMMENT '活动开始时间',
  `draw_time` datetime NOT NULL COMMENT '活动开奖时间',
  `title` varchar(256) NOT NULL COMMENT '抽奖标题',
  `cost_score` int(10) NOT NULL DEFAULT '0' COMMENT '消耗幸运星',
  `sponsor_id` int(10) NOT NULL COMMENT '赞助商ID',
  `join_limit` tinyint(4) NOT NULL DEFAULT '1' COMMENT '参与活动类型, 1表示用户只能参加一次，2表示用户每天可以参加一次',
  `join_num` int(10) NOT NULL DEFAULT '0' COMMENT '实际参与人数',
  `draw_limit` int(10) NOT NULL DEFAULT '0' COMMENT '最低开奖人数',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '抽奖活动描述',
  `show_num` int(10) NOT NULL DEFAULT '0' COMMENT '初始参与人数',
  `window_config` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '活动配置',
  `get_prize_num` int(10) NOT NULL DEFAULT '0' COMMENT '获取奖品的数量',
  `sort` int(8) NOT NULL DEFAULT '0' COMMENT '优先级',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `delete_time` datetime NOT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖活动管理';



CREATE TABLE `lotto_prize_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `lotto_activity_id` int(10) NOT NULL COMMENT '抽奖活动ID',
  `prize_id` int(10) NOT NULL COMMENT '抽奖活动奖品ID',
  `prize_num` tinyint(4) NOT NULL DEFAULT '0' COMMENT '奖品数量',
  `award_desc` varchar(128) NOT NULL DEFAULT '' COMMENT '奖项名，一等奖，二等奖',
  `show_num` tinyint(4) NOT NULL DEFAULT '0' COMMENT '展示数量',
  `prize_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '奖品类型 1表示实物，2表示虚拟',
  `virtual_label` varchar(128) NOT NULL DEFAULT '' COMMENT '虚拟奖品的标签',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '优先级',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `delete_time` datetime NOT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_unique_key` (`lotto_activity_id`, `prize_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖活动奖品配置';



CREATE TABLE `lotto_prize` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '奖品名称',
  `image_url` varchar(256) NOT NULL DEFAULT '' COMMENT '奖品图片地址',
  `sku_id` int(10) NOT NULL DEFAULT '0' COMMENT '奖品单品ID',
  `sku_name` varchar(128) NOT NULL DEFAULT '' COMMENT '奖品单品名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `delete_time` datetime NOT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_unique_key` (`sku_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖活动奖品';



CREATE TABLE `lotto_participate_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `lotto_activity_id` int(10) NOT NULL COMMENT '抽奖活动ID',
  `uid` int(10) NOT NULL COMMENT '参与用户ID',
  `cost_score` int(10) NOT NULL DEFAULT '0' COMMENT '消耗幸运星',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '中奖状态，0表示未中奖，1表示中奖',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖活动参与记录';



CREATE TABLE `lotto_reward_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `lotto_activity_id` int(10) NOT NULL COMMENT '抽奖活动ID',
  `project_id` int(10) NOT NULL COMMENT '奖项ID，对应lotto_prize_config的ID',
  `project_name` varchar(128) NOT NULL DEFAULT '' COMMENT '奖项名称',
  `prize_id` int(10) NOT NULL COMMENT '抽奖活动奖品ID',
  `uid` int(10) NOT NULL COMMENT '参与用户ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '中奖姓名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '中奖用户昵称',
  `avatar` varchar(256) NOT NULL DEFAULT '' COMMENT '头像',
  `mobile` varchar(18) NOT NULL DEFAULT '' COMMENT '手机号',
  `address` varchar(256) NOT NULL DEFAULT '' COMMENT '地址',
  `award_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '类型 1表示实物，2表示虚拟',
  `award_value` int(10) NOT NULL DEFAULT '0' COMMENT '虚拟中奖金额',
  `fast_num` varchar(36) NOT NULL DEFAULT '' COMMENT '',
  `fast_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '中奖商品发放状态 0表示未发货，1表示已发货',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_reward_key` (`lotto_activity_id`, `uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖活动中奖记录';



CREATE TABLE `lotto_sponsor` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '赞助商名称',
  `icon_url` varchar(256) NOT NULL DEFAULT '' COMMENT '图标icon',
  `app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '赞助商小程序APP_ID',
  `path_url` varchar(128) NOT NULL DEFAULT '' COMMENT '跳转下的url',
  `content` varchar(512) NOT NULL DEFAULT '' COMMENT '小程序介绍内容',
  `share_text` varchar(512) NOT NULL DEFAULT '' COMMENT '分享文案',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开启 1表示开启，0表示关闭',
  `self_support` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开启 1表示自营，0表示非自营',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '赞助商介绍',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖活动赞助商';

```
