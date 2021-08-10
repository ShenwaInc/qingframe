<?php

if (!isset($prefix)){
    $prefix = 'ims_';
}

$setupsql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$prefix}account` (
  `acid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `hash` varchar(8) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `isconnect` tinyint(4) NOT NULL,
  `isdeleted` tinyint(3) UNSIGNED NOT NULL,
  `endtime` int(10) UNSIGNED NOT NULL,
  `send_account_expire_status` tinyint(1) NOT NULL,
  `send_api_expire_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`acid`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_aliapp` (
  `acid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uniacid` int(10) NOT NULL,
  `level` tinyint(4) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  `key` varchar(16) NOT NULL,
  PRIMARY KEY (`acid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_baiduapp` (
  `acid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uniacid` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `appid` varchar(32) NOT NULL,
  `key` varchar(32) NOT NULL,
  `secret` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`acid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_phoneapp` (
  `acid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `uniacid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`acid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_toutiaoapp` (
  `acid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uniacid` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `appid` varchar(32) NOT NULL,
  `key` varchar(32) NOT NULL,
  `secret` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`acid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_webapp` (
  `acid` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`acid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_wechats` (
  `acid` int(10) UNSIGNED NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `token` varchar(32) NOT NULL,
  `encodingaeskey` varchar(255) NOT NULL,
  `level` tinyint(4) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `account` varchar(30) NOT NULL,
  `original` varchar(50) NOT NULL,
  `signature` varchar(100) NOT NULL,
  `country` varchar(10) NOT NULL,
  `province` varchar(3) NOT NULL,
  `city` varchar(15) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `lastupdate` int(10) UNSIGNED NOT NULL,
  `key` varchar(50) NOT NULL,
  `secret` varchar(50) NOT NULL,
  `styleid` int(10) UNSIGNED NOT NULL,
  `subscribeurl` varchar(120) NOT NULL,
  `auth_refresh_token` varchar(255) NOT NULL,
  PRIMARY KEY (`acid`),
  KEY `idx_key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_wxapp` (
  `acid` int(10) UNSIGNED NOT NULL,
  `uniacid` int(10) NOT NULL,
  `token` varchar(32) NOT NULL,
  `encodingaeskey` varchar(43) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `account` varchar(30) NOT NULL,
  `original` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `secret` varchar(50) NOT NULL,
  `name` varchar(30) NOT NULL,
  `appdomain` varchar(255) NOT NULL,
  `auth_refresh_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`acid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}account_xzapp` (
  `acid` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `original` varchar(50) NOT NULL,
  `lastupdate` int(10) NOT NULL,
  `styleid` int(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  `token` varchar(32) NOT NULL,
  `encodingaeskey` varchar(255) NOT NULL,
  `xzapp_id` varchar(30) NOT NULL,
  `level` tinyint(4) UNSIGNED NOT NULL,
  `key` varchar(80) NOT NULL,
  `secret` varchar(80) NOT NULL,
  PRIMARY KEY (`acid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}activity_clerks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `storeid` int(10) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `password` (`password`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}activity_clerk_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `displayorder` int(4) NOT NULL,
  `pid` int(6) NOT NULL,
  `group_name` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `permission` varchar(50) NOT NULL,
  `system` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}article_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `displayorder` tinyint(3) UNSIGNED NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}article_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `articleid` int(10) NOT NULL,
  `parentid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `content` varchar(500) DEFAULT NULL,
  `is_like` tinyint(1) NOT NULL,
  `is_reply` tinyint(1) NOT NULL,
  `like_num` int(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `articleid` (`articleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}article_news` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cateid` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `author` varchar(50) NOT NULL,
  `displayorder` tinyint(3) UNSIGNED NOT NULL,
  `is_display` tinyint(3) UNSIGNED NOT NULL,
  `is_show_home` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `click` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}article_notice` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cateid` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `displayorder` tinyint(3) UNSIGNED NOT NULL,
  `is_display` tinyint(3) UNSIGNED NOT NULL,
  `is_show_home` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `click` int(10) UNSIGNED NOT NULL,
  `style` varchar(200) NOT NULL,
  `group` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}article_unread_notice` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `notice_id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `is_new` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `notice_id` (`notice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}attachment_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}basic_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `content` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}business` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `weid` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `qq` varchar(15) NOT NULL,
  `province` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `dist` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `lng` varchar(10) NOT NULL,
  `lat` varchar(10) NOT NULL,
  `industry1` varchar(10) NOT NULL,
  `industry2` varchar(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lat_lng` (`lng`,`lat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_attachment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `module_upload_dir` varchar(100) NOT NULL,
  `group_id` int(11) NOT NULL,
  `displayorder` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_cache` (
  `key` varchar(100) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_cron` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cloudid` int(10) UNSIGNED NOT NULL,
  `module` varchar(50) NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `filename` varchar(50) NOT NULL,
  `lastruntime` int(10) UNSIGNED NOT NULL,
  `nextruntime` int(10) UNSIGNED NOT NULL,
  `weekday` tinyint(3) NOT NULL,
  `day` tinyint(3) NOT NULL,
  `hour` tinyint(3) NOT NULL,
  `minute` varchar(255) NOT NULL,
  `extra` varchar(5000) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `createtime` (`createtime`),
  KEY `nextruntime` (`nextruntime`),
  KEY `uniacid` (`uniacid`),
  KEY `cloudid` (`cloudid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_cron_record` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `module` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `tid` int(10) UNSIGNED NOT NULL,
  `note` varchar(500) NOT NULL,
  `tag` varchar(5000) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `tid` (`tid`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `payload` varchar(255) NOT NULL,
  `status` tinyint(3) NOT NULL,
  `title` varchar(22) NOT NULL,
  `handled` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `updatetime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_menu` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL,
  `title` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `url` varchar(255) NOT NULL,
  `append_title` varchar(30) NOT NULL,
  `append_url` varchar(255) NOT NULL,
  `displayorder` tinyint(3) UNSIGNED NOT NULL,
  `type` varchar(15) NOT NULL,
  `is_display` tinyint(3) UNSIGNED NOT NULL,
  `is_system` tinyint(3) UNSIGNED NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `group_name` varchar(30) NOT NULL,
  `icon` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_menu_shortcut` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `uniacid` int(10) NOT NULL,
  `modulename` varchar(100) NOT NULL,
  `displayorder` int(10) NOT NULL,
  `position` varchar(100) NOT NULL,
  `updatetime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_paylog` (
  `plid` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `acid` int(10) NOT NULL,
  `openid` varchar(40) NOT NULL,
  `uniontid` varchar(64) NOT NULL,
  `tid` varchar(128) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `module` varchar(50) NOT NULL,
  `tag` varchar(2000) NOT NULL,
  `is_usecard` tinyint(3) UNSIGNED NOT NULL,
  `card_type` tinyint(3) UNSIGNED NOT NULL,
  `card_id` varchar(50) NOT NULL,
  `card_fee` decimal(10,2) UNSIGNED NOT NULL,
  `encrypt_code` varchar(100) NOT NULL,
  `is_wish` tinyint(1) NOT NULL,
  `coupon` varchar(1000) NOT NULL,
  PRIMARY KEY (`plid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_tid` (`tid`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `uniontid` (`uniontid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_performance` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `runtime` varchar(10) NOT NULL,
  `runurl` varchar(512) NOT NULL,
  `runsql` varchar(512) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_queue` (
  `qid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `message` varchar(2000) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `keyword` varchar(1000) NOT NULL,
  `response` varchar(2000) NOT NULL,
  `module` varchar(50) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `dateline` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`qid`),
  KEY `uniacid` (`uniacid`,`acid`),
  KEY `module` (`module`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_refundlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `refund_uniontid` varchar(64) NOT NULL,
  `reason` varchar(80) NOT NULL,
  `uniontid` varchar(64) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `status` int(2) NOT NULL,
  `is_wish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refund_uniontid` (`refund_uniontid`),
  KEY `uniontid` (`uniontid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_resource` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `media_id` varchar(100) NOT NULL,
  `trunk` int(10) UNSIGNED NOT NULL,
  `type` varchar(10) NOT NULL,
  `dateline` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`mid`),
  KEY `acid` (`uniacid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_sendsms_log` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `result` varchar(255) NOT NULL,
  `createtime` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_sessions` (
  `sid` char(32) NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `openid` varchar(50) NOT NULL,
  `data` varchar(2000) NOT NULL,
  `expiretime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}core_settings` (
  `key` varchar(200) NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}coupon_location` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `sid` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `business_name` varchar(50) NOT NULL,
  `branch_name` varchar(50) NOT NULL,
  `category` varchar(255) NOT NULL,
  `province` varchar(15) NOT NULL,
  `city` varchar(15) NOT NULL,
  `district` varchar(15) NOT NULL,
  `address` varchar(50) NOT NULL,
  `longitude` varchar(15) NOT NULL,
  `latitude` varchar(15) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `photo_list` varchar(10000) NOT NULL,
  `avg_price` int(10) UNSIGNED NOT NULL,
  `open_time` varchar(50) NOT NULL,
  `recommend` varchar(255) NOT NULL,
  `special` varchar(255) NOT NULL,
  `introduction` varchar(255) NOT NULL,
  `offset_type` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`acid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}cover_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `multiid` int(10) UNSIGNED NOT NULL,
  `rid` int(10) UNSIGNED NOT NULL,
  `module` varchar(30) NOT NULL,
  `do` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}custom_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `start1` int(10) NOT NULL,
  `end1` int(10) NOT NULL,
  `start2` int(10) NOT NULL,
  `end2` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}images_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `mediaid` varchar(255) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_cash_record` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `clerk_id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `clerk_type` tinyint(3) UNSIGNED NOT NULL,
  `fee` decimal(10,2) UNSIGNED NOT NULL,
  `final_fee` decimal(10,2) UNSIGNED NOT NULL,
  `credit1` int(10) UNSIGNED NOT NULL,
  `credit1_fee` decimal(10,2) UNSIGNED NOT NULL,
  `credit2` decimal(10,2) UNSIGNED NOT NULL,
  `cash` decimal(10,2) UNSIGNED NOT NULL,
  `return_cash` decimal(10,2) UNSIGNED NOT NULL,
  `final_cash` decimal(10,2) UNSIGNED NOT NULL,
  `remark` varchar(255) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `trade_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_chats_record` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `flag` tinyint(3) UNSIGNED NOT NULL,
  `openid` varchar(32) NOT NULL,
  `msgtype` varchar(15) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`acid`),
  KEY `openid` (`openid`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_credits_recharge` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `openid` varchar(50) NOT NULL,
  `tid` varchar(64) NOT NULL,
  `transid` varchar(30) NOT NULL,
  `fee` varchar(10) NOT NULL,
  `type` varchar(15) NOT NULL,
  `tag` varchar(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `backtype` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid_uid` (`uniacid`,`uid`),
  KEY `idx_tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_credits_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL,
  `uniacid` int(11) NOT NULL,
  `credittype` varchar(10) NOT NULL,
  `num` decimal(10,2) NOT NULL,
  `operator` int(10) UNSIGNED NOT NULL,
  `module` varchar(30) NOT NULL,
  `clerk_id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `clerk_type` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `remark` varchar(200) NOT NULL,
  `real_uniacid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_fans_groups` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `groups` varchar(10000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_fans_tag` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `fanid` int(11) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `subscribe` int(11) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `sex` int(11) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `headimgurl` varchar(150) DEFAULT NULL,
  `subscribe_time` int(11) NOT NULL,
  `unionid` varchar(100) DEFAULT NULL,
  `remark` varchar(250) DEFAULT NULL,
  `groupid` varchar(100) DEFAULT NULL,
  `tagid_list` varchar(250) DEFAULT NULL,
  `subscribe_scene` varchar(100) DEFAULT NULL,
  `qr_scene_str` varchar(250) DEFAULT NULL,
  `qr_scene` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fanid` (`fanid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_fans_tag_mapping` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fanid` int(11) UNSIGNED NOT NULL,
  `tagid` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mapping` (`fanid`,`tagid`),
  KEY `fanid_index` (`fanid`),
  KEY `tagid_index` (`tagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_groups` (
  `groupid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  `credit` int(10) UNSIGNED NOT NULL,
  `isdefault` tinyint(4) NOT NULL,
  PRIMARY KEY (`groupid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_handsel` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `touid` int(10) UNSIGNED NOT NULL,
  `fromuid` varchar(32) NOT NULL,
  `module` varchar(30) NOT NULL,
  `sign` varchar(100) NOT NULL,
  `action` varchar(20) NOT NULL,
  `credit_value` int(10) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`touid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_mapping_fans` (
  `fanid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `acid` int(10) UNSIGNED NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `groupid` varchar(60) NOT NULL,
  `salt` char(8) NOT NULL,
  `follow` tinyint(1) UNSIGNED NOT NULL,
  `followtime` int(10) UNSIGNED NOT NULL,
  `unfollowtime` int(10) UNSIGNED NOT NULL,
  `tag` varchar(1000) NOT NULL,
  `updatetime` int(10) UNSIGNED DEFAULT NULL,
  `unionid` varchar(64) NOT NULL,
  `user_from` tinyint(1) NOT NULL,
  PRIMARY KEY (`fanid`),
  UNIQUE KEY `openid_2` (`openid`),
  KEY `acid` (`acid`),
  KEY `uniacid` (`uniacid`),
  KEY `nickname` (`nickname`),
  KEY `updatetime` (`updatetime`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_mass_record` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `groupname` varchar(50) NOT NULL,
  `fansnum` int(10) UNSIGNED NOT NULL,
  `msgtype` varchar(10) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `group` int(10) NOT NULL,
  `attach_id` int(10) UNSIGNED NOT NULL,
  `media_id` varchar(100) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `cron_id` int(10) UNSIGNED NOT NULL,
  `sendtime` int(10) UNSIGNED NOT NULL,
  `finalsendtime` int(10) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `msg_id` varchar(50) NOT NULL,
  `msg_data_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`acid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_members` (
  `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `mobile` varchar(18) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `groupid` int(11) NOT NULL,
  `credit1` decimal(10,2) UNSIGNED NOT NULL,
  `credit2` decimal(10,2) UNSIGNED NOT NULL,
  `credit3` decimal(10,2) UNSIGNED NOT NULL,
  `credit4` decimal(10,2) UNSIGNED NOT NULL,
  `credit5` decimal(10,2) UNSIGNED NOT NULL,
  `credit6` decimal(10,2) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `realname` varchar(10) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `qq` varchar(15) NOT NULL,
  `vip` tinyint(3) UNSIGNED NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `birthyear` smallint(6) UNSIGNED NOT NULL,
  `birthmonth` tinyint(3) UNSIGNED NOT NULL,
  `birthday` tinyint(3) UNSIGNED NOT NULL,
  `constellation` varchar(10) NOT NULL,
  `zodiac` varchar(5) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `idcard` varchar(30) NOT NULL,
  `studentid` varchar(50) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `nationality` varchar(30) NOT NULL,
  `resideprovince` varchar(30) NOT NULL,
  `residecity` varchar(30) NOT NULL,
  `residedist` varchar(30) NOT NULL,
  `graduateschool` varchar(50) NOT NULL,
  `company` varchar(50) NOT NULL,
  `education` varchar(10) NOT NULL,
  `occupation` varchar(30) NOT NULL,
  `position` varchar(30) NOT NULL,
  `revenue` varchar(10) NOT NULL,
  `affectivestatus` varchar(30) NOT NULL,
  `lookingfor` varchar(255) NOT NULL,
  `bloodtype` varchar(5) NOT NULL,
  `height` varchar(5) NOT NULL,
  `weight` varchar(5) NOT NULL,
  `alipay` varchar(30) NOT NULL,
  `msn` varchar(30) NOT NULL,
  `taobao` varchar(30) NOT NULL,
  `site` varchar(30) NOT NULL,
  `bio` text NOT NULL,
  `interest` text NOT NULL,
  `pay_password` varchar(30) NOT NULL,
  `user_from` tinyint(1) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `groupid` (`groupid`),
  KEY `uniacid` (`uniacid`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_member_address` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(50) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `zipcode` varchar(6) NOT NULL,
  `province` varchar(32) NOT NULL,
  `city` varchar(32) NOT NULL,
  `district` varchar(32) NOT NULL,
  `address` varchar(512) NOT NULL,
  `isdefault` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uinacid` (`uniacid`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_member_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `fieldid` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `available` tinyint(1) NOT NULL,
  `displayorder` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_fieldid` (`fieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_member_property` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `property` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mc_oauth_fans` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `oauth_openid` varchar(50) NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `openid` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_oauthopenid_acid` (`oauth_openid`,`acid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}menu_event` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `keyword` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `picmd5` varchar(32) NOT NULL,
  `openid` varchar(128) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `picmd5` (`picmd5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}message_notice_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(3) NOT NULL,
  `uid` int(11) NOT NULL,
  `sign` varchar(22) NOT NULL,
  `type` tinyint(3) NOT NULL,
  `status` tinyint(3) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}mobilenumber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(10) NOT NULL,
  `enabled` tinyint(1) UNSIGNED NOT NULL,
  `dateline` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules` (
  `mid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `application_type` tinyint(1) UNSIGNED NOT NULL,
  `type` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `version` varchar(15) NOT NULL,
  `ability` varchar(500) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `author` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `settings` tinyint(1) NOT NULL,
  `subscribes` varchar(500) NOT NULL,
  `handles` varchar(500) NOT NULL,
  `isrulefields` tinyint(1) NOT NULL,
  `issystem` tinyint(1) UNSIGNED NOT NULL,
  `target` int(10) UNSIGNED NOT NULL,
  `iscard` tinyint(3) UNSIGNED NOT NULL,
  `permissions` varchar(5000) NOT NULL,
  `title_initial` varchar(1) NOT NULL,
  `wxapp_support` tinyint(1) NOT NULL,
  `welcome_support` int(2) NOT NULL,
  `oauth_type` tinyint(1) NOT NULL,
  `webapp_support` tinyint(1) NOT NULL,
  `phoneapp_support` tinyint(1) NOT NULL,
  `account_support` tinyint(1) NOT NULL,
  `xzapp_support` tinyint(1) NOT NULL,
  `aliapp_support` tinyint(1) NOT NULL,
  `logo` varchar(250) NOT NULL,
  `baiduapp_support` tinyint(1) NOT NULL,
  `toutiaoapp_support` tinyint(1) NOT NULL,
  `from` varchar(10) NOT NULL,
  `cloud_record` tinyint(1) NOT NULL,
  `sections` int(10) UNSIGNED NOT NULL,
  `label` varchar(500) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`mid`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules_bindings` (
  `eid` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `entry` varchar(30) NOT NULL,
  `call` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `do` varchar(200) NOT NULL,
  `state` varchar(200) NOT NULL,
  `direct` int(11) NOT NULL,
  `url` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `displayorder` tinyint(255) UNSIGNED NOT NULL,
  `multilevel` tinyint(1) NOT NULL,
  `parent` varchar(50) NOT NULL,
  PRIMARY KEY (`eid`),
  KEY `idx_module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules_cloud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `application_type` tinyint(1) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `title_initial` varchar(1) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `version` varchar(10) NOT NULL,
  `install_status` tinyint(4) NOT NULL,
  `account_support` tinyint(4) NOT NULL,
  `wxapp_support` tinyint(4) NOT NULL,
  `webapp_support` tinyint(4) NOT NULL,
  `phoneapp_support` tinyint(4) NOT NULL,
  `welcome_support` tinyint(4) NOT NULL,
  `main_module_name` varchar(50) NOT NULL,
  `main_module_logo` varchar(100) NOT NULL,
  `has_new_version` tinyint(1) NOT NULL,
  `has_new_branch` tinyint(1) NOT NULL,
  `is_ban` tinyint(4) NOT NULL,
  `lastupdatetime` int(11) NOT NULL,
  `xzapp_support` tinyint(1) NOT NULL,
  `cloud_id` int(11) NOT NULL,
  `aliapp_support` tinyint(1) NOT NULL,
  `baiduapp_support` tinyint(1) NOT NULL,
  `toutiaoapp_support` tinyint(1) NOT NULL,
  `buytime` int(10) NOT NULL,
  `module_status` tinyint(1) NOT NULL,
  `label` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `lastupdatetime` (`lastupdatetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules_ignore` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `version` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `main_module` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `main_module` (`main_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules_plugin_rank` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `rank` int(10) NOT NULL,
  `plugin_name` varchar(200) NOT NULL,
  `main_module_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules_rank` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL,
  `rank` int(10) NOT NULL,
  `uniacid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_name` (`module_name`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}modules_recycle` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `account_support` tinyint(1) NOT NULL,
  `wxapp_support` tinyint(1) NOT NULL,
  `welcome_support` tinyint(1) NOT NULL,
  `webapp_support` tinyint(1) NOT NULL,
  `phoneapp_support` tinyint(1) NOT NULL,
  `xzapp_support` tinyint(1) NOT NULL,
  `aliapp_support` tinyint(1) NOT NULL,
  `baiduapp_support` tinyint(1) NOT NULL,
  `toutiaoapp_support` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}music_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(300) NOT NULL,
  `hqurl` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}news_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) NOT NULL,
  `title` varchar(50) NOT NULL,
  `author` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `thumb` varchar(500) NOT NULL,
  `content` mediumtext NOT NULL,
  `url` varchar(255) NOT NULL,
  `displayorder` int(10) NOT NULL,
  `incontent` tinyint(1) NOT NULL,
  `createtime` int(10) NOT NULL,
  `media_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}phoneapp_versions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `version` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `modules` text NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}profile_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field` varchar(255) NOT NULL,
  `available` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `displayorder` smallint(6) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `unchangeable` tinyint(1) NOT NULL,
  `showinregister` tinyint(1) NOT NULL,
  `field_length` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}qrcode` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `type` varchar(10) NOT NULL,
  `extra` int(10) UNSIGNED NOT NULL,
  `qrcid` bigint(20) NOT NULL,
  `scene_str` varchar(64) NOT NULL,
  `name` varchar(50) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `model` tinyint(1) UNSIGNED NOT NULL,
  `ticket` varchar(250) NOT NULL,
  `url` varchar(256) NOT NULL,
  `expire` int(10) UNSIGNED NOT NULL,
  `subnum` int(10) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_qrcid` (`qrcid`),
  KEY `uniacid` (`uniacid`),
  KEY `ticket` (`ticket`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}qrcode_stat` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `qid` int(10) UNSIGNED NOT NULL,
  `openid` varchar(50) NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `qrcid` bigint(20) UNSIGNED NOT NULL,
  `scene_str` varchar(64) NOT NULL,
  `name` varchar(50) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}rule` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `displayorder` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `containtype` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}rule_keyword` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `module` varchar(50) NOT NULL,
  `content` varchar(255) NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `displayorder` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_content` (`content`),
  KEY `rid` (`rid`),
  KEY `idx_rid` (`rid`),
  KEY `idx_uniacid_type_content` (`uniacid`,`type`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_article` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `rid` int(10) UNSIGNED NOT NULL,
  `iscommend` tinyint(1) NOT NULL,
  `ishot` tinyint(1) UNSIGNED NOT NULL,
  `pcate` int(10) UNSIGNED NOT NULL,
  `ccate` int(10) UNSIGNED NOT NULL,
  `template` varchar(300) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `incontent` tinyint(1) NOT NULL,
  `source` varchar(255) NOT NULL,
  `author` varchar(50) NOT NULL,
  `displayorder` int(10) UNSIGNED NOT NULL,
  `linkurl` varchar(500) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `edittime` int(10) NOT NULL,
  `click` int(10) UNSIGNED NOT NULL,
  `type` varchar(10) NOT NULL,
  `credit` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_iscommend` (`iscommend`),
  KEY `idx_ishot` (`ishot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_article_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `articleid` int(10) NOT NULL,
  `parentid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `content` text,
  `is_read` tinyint(1) NOT NULL,
  `iscomment` tinyint(1) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `articleid` (`articleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `nid` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `parentid` int(10) UNSIGNED NOT NULL,
  `displayorder` tinyint(3) UNSIGNED NOT NULL,
  `enabled` tinyint(1) UNSIGNED NOT NULL,
  `icon` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `styleid` int(10) UNSIGNED NOT NULL,
  `linkurl` varchar(500) NOT NULL,
  `ishomepage` tinyint(1) NOT NULL,
  `icontype` tinyint(1) UNSIGNED NOT NULL,
  `css` varchar(500) NOT NULL,
  `multiid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_multi` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `title` varchar(30) NOT NULL,
  `styleid` int(10) UNSIGNED NOT NULL,
  `site_info` text NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `bindhost` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `bindhost` (`bindhost`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_nav` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `multiid` int(10) UNSIGNED NOT NULL,
  `section` tinyint(4) NOT NULL,
  `module` varchar(50) NOT NULL,
  `displayorder` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `position` tinyint(4) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(500) NOT NULL,
  `css` varchar(1000) NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `categoryid` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `multiid` (`multiid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_page` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `multiid` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `params` longtext NOT NULL,
  `html` longtext NOT NULL,
  `multipage` longtext NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `goodnum` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `multiid` (`multiid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_slide` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `multiid` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `displayorder` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `multiid` (`multiid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_store_cash_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `founder_uid` int(10) NOT NULL,
  `number` char(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `founder_uid` (`founder_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_store_cash_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `number` char(30) NOT NULL,
  `founder_uid` int(10) NOT NULL,
  `order_id` int(10) NOT NULL,
  `goods_id` int(10) NOT NULL,
  `order_amount` decimal(10,2) NOT NULL,
  `cash_log_id` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `founder_uid` (`founder_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_store_create_account` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `uniacid` int(10) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `endtime` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_store_goods` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `title` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `account_num` int(10) NOT NULL,
  `wxapp_num` int(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(15) NOT NULL,
  `slide` varchar(1000) NOT NULL,
  `category_id` int(10) NOT NULL,
  `title_initial` varchar(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createtime` int(10) NOT NULL,
  `synopsis` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `module_group` int(10) NOT NULL,
  `api_num` int(10) NOT NULL,
  `user_group_price` varchar(1000) NOT NULL,
  `user_group` int(10) NOT NULL,
  `account_group` int(10) NOT NULL,
  `is_wish` tinyint(1) NOT NULL,
  `logo` varchar(300) NOT NULL,
  `platform_num` int(10) NOT NULL,
  `aliapp_num` int(10) NOT NULL,
  `baiduapp_num` int(10) NOT NULL,
  `phoneapp_num` int(10) NOT NULL,
  `toutiaoapp_num` int(10) NOT NULL,
  `webapp_num` int(10) NOT NULL,
  `xzapp_num` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `category_id` (`category_id`),
  KEY `price` (`price`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_store_goods_cloud` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cloud_id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `logo` varchar(300) NOT NULL,
  `wish_branch` int(10) NOT NULL,
  `is_edited` tinyint(1) NOT NULL,
  `isdeleted` tinyint(1) NOT NULL,
  `branchs` varchar(6000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cloud_id` (`cloud_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_store_order` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderid` varchar(28) NOT NULL,
  `goodsid` int(10) NOT NULL,
  `duration` int(10) NOT NULL,
  `buyer` varchar(50) NOT NULL,
  `buyerid` int(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `changeprice` tinyint(1) NOT NULL,
  `createtime` int(10) NOT NULL,
  `uniacid` int(10) NOT NULL,
  `endtime` int(15) NOT NULL,
  `wxapp` int(15) NOT NULL,
  `is_wish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goodid` (`goodsid`),
  KEY `buyerid` (`buyerid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_styles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `templateid` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_styles_vars` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `templateid` int(10) UNSIGNED NOT NULL,
  `styleid` int(10) UNSIGNED NOT NULL,
  `variable` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}site_templates` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `version` varchar(64) NOT NULL,
  `description` varchar(500) NOT NULL,
  `author` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `sections` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}stat_fans` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `new` int(10) UNSIGNED NOT NULL,
  `cancel` int(10) UNSIGNED NOT NULL,
  `cumulate` int(10) NOT NULL,
  `date` varchar(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}stat_keyword` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `rid` varchar(10) NOT NULL,
  `kid` int(10) UNSIGNED NOT NULL,
  `hit` int(10) UNSIGNED NOT NULL,
  `lastupdate` int(10) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}stat_msg_history` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `rid` int(10) UNSIGNED NOT NULL,
  `kid` int(10) UNSIGNED NOT NULL,
  `from_user` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `type` varchar(10) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}stat_rule` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `rid` int(10) UNSIGNED NOT NULL,
  `hit` int(10) UNSIGNED NOT NULL,
  `lastupdate` int(10) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_createtime` (`createtime`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}stat_visit` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `module` varchar(100) NOT NULL,
  `count` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `type` varchar(10) NOT NULL,
  `ip_count` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `module` (`module`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}stat_visit_ip` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` bigint(10) NOT NULL,
  `uniacid` int(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `module` varchar(100) NOT NULL,
  `date` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_date_module_uniacid` (`ip`,`date`,`module`,`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}system_stat_visit` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `modulename` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL,
  `displayorder` int(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  `updatetime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}system_welcome_binddomain` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `module_name` varchar(50) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_name` (`module_name`),
  KEY `uid` (`uid`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_account` (
  `uniacid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupid` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `default_acid` int(10) UNSIGNED NOT NULL,
  `rank` int(10) DEFAULT NULL,
  `title_initial` varchar(1) NOT NULL,
  `createtime` int(10) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `qrcode` varchar(255) NOT NULL,
  `create_uid` int(11) NOT NULL,
  PRIMARY KEY (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_account_extra_modules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `modules` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_account_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `groupid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_account_menus` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `menuid` int(10) UNSIGNED NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(30) NOT NULL,
  `sex` tinyint(3) UNSIGNED NOT NULL,
  `group_id` int(10) NOT NULL,
  `client_platform_type` tinyint(3) UNSIGNED NOT NULL,
  `area` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `isdeleted` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `menuid` (`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_account_modules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `module` varchar(50) NOT NULL,
  `enabled` tinyint(1) UNSIGNED NOT NULL,
  `settings` text NOT NULL,
  `shortcut` tinyint(1) UNSIGNED NOT NULL,
  `displayorder` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_module` (`module`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_account_modules_shortcut` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `url` varchar(250) NOT NULL,
  `icon` varchar(200) NOT NULL,
  `uniacid` int(10) NOT NULL,
  `version_id` int(10) NOT NULL,
  `module_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_account_users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL,
  `rank` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_memberid` (`uid`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_uid` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `modules` text NOT NULL,
  `templates` varchar(5000) NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_link_uniacid` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `link_uniacid` int(10) NOT NULL,
  `version_id` int(10) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_modules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `module_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_settings` (
  `uniacid` int(10) UNSIGNED NOT NULL,
  `passport` varchar(200) NOT NULL,
  `oauth` varchar(100) NOT NULL,
  `jsauth_acid` int(10) UNSIGNED NOT NULL,
  `notify` varchar(2000) NOT NULL,
  `creditnames` varchar(500) NOT NULL,
  `creditbehaviors` varchar(500) NOT NULL,
  `welcome` varchar(60) NOT NULL,
  `default` varchar(60) NOT NULL,
  `default_message` varchar(2000) NOT NULL,
  `payment` text NOT NULL,
  `stat` varchar(300) NOT NULL,
  `default_site` int(10) UNSIGNED DEFAULT NULL,
  `sync` tinyint(3) UNSIGNED NOT NULL,
  `recharge` varchar(500) NOT NULL,
  `tplnotice` varchar(2000) NOT NULL,
  `grouplevel` tinyint(3) UNSIGNED NOT NULL,
  `mcplugin` varchar(500) NOT NULL,
  `exchange_enable` tinyint(3) UNSIGNED NOT NULL,
  `coupon_type` tinyint(3) UNSIGNED NOT NULL,
  `menuset` text NOT NULL,
  `statistics` varchar(100) NOT NULL,
  `bind_domain` varchar(200) NOT NULL,
  `comment_status` tinyint(1) NOT NULL,
  `reply_setting` tinyint(4) NOT NULL,
  `default_module` varchar(100) NOT NULL,
  `attachment_limit` int(11) NOT NULL,
  `attachment_size` varchar(20) NOT NULL,
  `sync_member` tinyint(1) NOT NULL,
  `remote` varchar(2000) NOT NULL,
  PRIMARY KEY (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}uni_verifycode` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `receiver` varchar(50) NOT NULL,
  `verifycode` varchar(6) NOT NULL,
  `total` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `failed_count` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}userapi_cache` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `lastupdate` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}userapi_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `description` varchar(300) NOT NULL,
  `apiurl` varchar(300) NOT NULL,
  `token` varchar(32) NOT NULL,
  `default_text` varchar(100) NOT NULL,
  `cachetime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users` (
  `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_uid` int(10) NOT NULL,
  `groupid` int(10) UNSIGNED NOT NULL,
  `founder_groupid` tinyint(4) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(200) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL,
  `joindate` int(10) UNSIGNED NOT NULL,
  `joinip` varchar(15) NOT NULL,
  `lastvisit` int(10) UNSIGNED NOT NULL,
  `lastip` varchar(15) NOT NULL,
  `remark` varchar(500) NOT NULL,
  `starttime` int(10) UNSIGNED NOT NULL,
  `endtime` int(10) UNSIGNED NOT NULL,
  `register_type` tinyint(3) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `welcome_link` tinyint(4) NOT NULL,
  `notice_setting` varchar(5000) NOT NULL,
  `is_bind` tinyint(1) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `bind_sign` varchar(50) NOT NULL,
  `third_type` tinyint(4) NOT NULL,
  `third_nickname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `bind_sign` (`bind_sign`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_create_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL,
  `maxaccount` int(10) NOT NULL,
  `maxwxapp` int(10) NOT NULL,
  `maxwebapp` int(10) NOT NULL,
  `maxphoneapp` int(10) NOT NULL,
  `maxxzapp` int(10) NOT NULL,
  `maxaliapp` int(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  `maxbaiduapp` int(10) NOT NULL,
  `maxtoutiaoapp` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_extra_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `uni_group_id` int(10) NOT NULL,
  `create_group_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `uni_group_id` (`uni_group_id`),
  KEY `create_group_id` (`create_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_extra_limit` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `maxaccount` int(10) NOT NULL,
  `maxwxapp` int(10) NOT NULL,
  `maxwebapp` int(10) NOT NULL,
  `maxphoneapp` int(10) NOT NULL,
  `maxxzapp` int(10) NOT NULL,
  `maxaliapp` int(10) NOT NULL,
  `timelimit` int(10) NOT NULL,
  `maxbaiduapp` int(10) NOT NULL,
  `maxtoutiaoapp` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_extra_modules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `support` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `module_name` (`module_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_extra_templates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `template_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_failed_login` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `username` varchar(32) NOT NULL,
  `count` tinyint(1) UNSIGNED NOT NULL,
  `lastupdate` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_username` (`ip`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_founder_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `package` varchar(5000) NOT NULL,
  `maxaccount` int(10) UNSIGNED NOT NULL,
  `timelimit` int(10) UNSIGNED NOT NULL,
  `maxwxapp` int(10) UNSIGNED NOT NULL,
  `maxwebapp` int(10) NOT NULL,
  `maxphoneapp` int(10) NOT NULL,
  `maxxzapp` int(10) NOT NULL,
  `maxaliapp` int(10) NOT NULL,
  `maxbaiduapp` int(10) NOT NULL,
  `maxtoutiaoapp` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_founder_own_create_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `founder_uid` int(10) NOT NULL,
  `create_group_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `founder_uid` (`founder_uid`),
  KEY `create_group_id` (`create_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_founder_own_uni_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `founder_uid` int(10) NOT NULL,
  `uni_group_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `founder_uid` (`founder_uid`),
  KEY `uni_group_id` (`uni_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_founder_own_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `founder_uid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `founder_uid` (`founder_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_founder_own_users_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `founder_uid` int(10) NOT NULL,
  `users_group_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `founder_uid` (`founder_uid`),
  KEY `users_group_id` (`users_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_uid` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `package` varchar(5000) NOT NULL,
  `maxaccount` int(10) UNSIGNED NOT NULL,
  `timelimit` int(10) UNSIGNED NOT NULL,
  `maxwxapp` int(10) UNSIGNED NOT NULL,
  `maxwebapp` int(10) NOT NULL,
  `maxphoneapp` int(10) NOT NULL,
  `maxxzapp` int(10) NOT NULL,
  `maxaliapp` int(10) NOT NULL,
  `maxbaiduapp` int(10) NOT NULL,
  `maxtoutiaoapp` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_invitation` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `fromuid` int(10) UNSIGNED NOT NULL,
  `inviteuid` int(10) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_lastuse` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `uniacid` int(10) NOT NULL,
  `modulename` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_login_logs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL,
  `ip` varchar(15) NOT NULL,
  `city` varchar(256) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_operate_history` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_operate_star` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `rank` int(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_permission` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `permission` varchar(10000) NOT NULL,
  `url` varchar(255) NOT NULL,
  `modules` text NOT NULL,
  `templates` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}users_profile` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `edittime` int(10) NOT NULL,
  `realname` varchar(10) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `qq` varchar(15) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `fakeid` varchar(30) NOT NULL,
  `vip` tinyint(3) UNSIGNED NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `birthyear` smallint(6) UNSIGNED NOT NULL,
  `birthmonth` tinyint(3) UNSIGNED NOT NULL,
  `birthday` tinyint(3) UNSIGNED NOT NULL,
  `constellation` varchar(10) NOT NULL,
  `zodiac` varchar(5) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `idcard` varchar(30) NOT NULL,
  `studentid` varchar(50) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `nationality` varchar(30) NOT NULL,
  `resideprovince` varchar(30) NOT NULL,
  `residecity` varchar(30) NOT NULL,
  `residedist` varchar(30) NOT NULL,
  `graduateschool` varchar(50) NOT NULL,
  `company` varchar(50) NOT NULL,
  `education` varchar(10) NOT NULL,
  `occupation` varchar(30) NOT NULL,
  `position` varchar(30) NOT NULL,
  `revenue` varchar(10) NOT NULL,
  `affectivestatus` varchar(30) NOT NULL,
  `lookingfor` varchar(255) NOT NULL,
  `bloodtype` varchar(5) NOT NULL,
  `height` varchar(5) NOT NULL,
  `weight` varchar(5) NOT NULL,
  `alipay` varchar(30) NOT NULL,
  `msn` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `taobao` varchar(30) NOT NULL,
  `site` varchar(30) NOT NULL,
  `bio` text NOT NULL,
  `interest` text NOT NULL,
  `workerid` varchar(64) NOT NULL,
  `is_send_mobile_status` tinyint(3) NOT NULL,
  `send_expire_status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}video_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `mediaid` varchar(255) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}voice_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `mediaid` varchar(255) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wechat_attachment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `acid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `media_id` varchar(255) NOT NULL,
  `width` int(10) UNSIGNED NOT NULL,
  `height` int(10) UNSIGNED NOT NULL,
  `type` varchar(15) NOT NULL,
  `model` varchar(25) NOT NULL,
  `tag` varchar(5000) NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `module_upload_dir` varchar(100) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `media_id` (`media_id`),
  KEY `acid` (`acid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wechat_news` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED DEFAULT NULL,
  `attach_id` int(10) UNSIGNED NOT NULL,
  `thumb_media_id` varchar(60) NOT NULL,
  `thumb_url` varchar(255) NOT NULL,
  `title` varchar(50) NOT NULL,
  `author` varchar(30) NOT NULL,
  `digest` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `content_source_url` varchar(200) NOT NULL,
  `show_cover_pic` tinyint(3) UNSIGNED NOT NULL,
  `url` varchar(200) NOT NULL,
  `displayorder` int(2) NOT NULL,
  `need_open_comment` tinyint(1) NOT NULL,
  `only_fans_can_comment` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `attach_id` (`attach_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wxapp_general_analysis` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `session_cnt` int(10) NOT NULL,
  `visit_pv` int(10) NOT NULL,
  `visit_uv` int(10) NOT NULL,
  `visit_uv_new` int(10) NOT NULL,
  `type` tinyint(2) NOT NULL,
  `stay_time_uv` varchar(10) NOT NULL,
  `stay_time_session` varchar(10) NOT NULL,
  `visit_depth` varchar(10) NOT NULL,
  `ref_date` varchar(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `ref_date` (`ref_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wxapp_register_version` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `version_id` int(10) NOT NULL,
  `auditid` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `reason` varchar(1000) NOT NULL,
  `upload_time` int(11) NOT NULL,
  `audit_info` text NOT NULL,
  `submit_info` text NOT NULL,
  `developer` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `auditid` (`auditid`),
  KEY `version_id` (`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wxapp_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `appid` varchar(50) NOT NULL,
  `pagepath` varchar(255) NOT NULL,
  `mediaid` varchar(50) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wxapp_undocodeaudit_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `version_id` int(10) NOT NULL,
  `auditid` int(11) NOT NULL,
  `revoke_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `version_id` (`version_id`),
  KEY `auditid` (`auditid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wxapp_versions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) UNSIGNED NOT NULL,
  `multiid` int(10) UNSIGNED NOT NULL,
  `version` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `modules` varchar(1000) NOT NULL,
  `design_method` tinyint(1) NOT NULL,
  `template` int(10) NOT NULL,
  `quickmenu` varchar(2500) NOT NULL,
  `createtime` int(10) NOT NULL,
  `type` int(2) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `appjson` text NOT NULL,
  `default_appjson` text NOT NULL,
  `use_default` tinyint(1) NOT NULL,
  `last_modules` varchar(1000) DEFAULT NULL,
  `tominiprogram` varchar(1000) NOT NULL,
  `upload_time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$prefix}wxcard_reply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` int(10) UNSIGNED NOT NULL,
  `title` varchar(30) NOT NULL,
  `card_id` varchar(50) NOT NULL,
  `cid` int(10) UNSIGNED NOT NULL,
  `brand_name` varchar(30) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `success` varchar(255) NOT NULL,
  `error` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOF;
return $setupsql;
