<?php

use yii\db\Migration;

/**
 * Class m181018_034015_init
 */
class m181018_034015_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->db = 'db_content';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        SET NAMES utf8mb4;
        
        CREATE TABLE `article` (
          `articleId` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章ID',
          `authorId` int(11) NOT NULL COMMENT '作者ID',
          `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '文章类型，1 微信文章 2 Ins',
          `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
          `subTitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '副标题',
          `summary` text COLLATE utf8mb4_unicode_ci COMMENT '内容摘要',
          `headImg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头图',
          `orderId` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
          `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '11:启用 0:禁用',
          `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (`articleId`),
          KEY `authorId` (`authorId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章表';
        
        
        CREATE TABLE `article_comment` (
          `commentId` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论ID',
          `articleId` int(11) NOT NULL COMMENT '内容ID',
          `parentId` int(11) NOT NULL DEFAULT '0' COMMENT '父ID(@用户)',
          `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
          `content` text COLLATE utf8mb4_unicode_ci COMMENT '评论内容',
          `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (`commentId`),
          KEY `articleId` (`articleId`),
          KEY `uid` (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='评论表';
        
        
        CREATE TABLE `article_forward` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '转发Id',
          `articleId` int(11) NOT NULL COMMENT '文章Id',
          `uid` int(11) NOT NULL COMMENT '用户Id',
          `cTime` int(11) NOT NULL COMMENT '创建时间',
          `uTime` int(11) NOT NULL COMMENT '更新时间',
          `deleteFlg` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标识：0正常，1删除',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='转发表';
        
        
        CREATE TABLE `article_praise` (
          `praiseId` int(11) NOT NULL AUTO_INCREMENT COMMENT '点赞ID',
          `articleId` int(11) NOT NULL COMMENT '内容ID',
          `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
          `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (`praiseId`),
          KEY `articleId` (`articleId`),
          KEY `uid` (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='点赞表';
        
        
        CREATE TABLE `article_static` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
          `articleId` int(11) NOT NULL COMMENT '内容ID',
          `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:封面',
          `url` text COMMENT '静态资源url',
          `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (`id`),
          KEY `articleId` (`articleId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章静态资源表';
        
        
        CREATE TABLE `author_attention` (
          `authorId` int(11) NOT NULL COMMENT '作者ID',
          `uid` int(11) NOT NULL COMMENT '用户ID',
          `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (`authorId`,`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='关注表';
        
        
        CREATE TABLE `notices` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息Id',
          `type` set('like','comment','follow','notice') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息类型',
          `content` json NOT NULL COMMENT '消息内容',
          `is_read` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已读，0未读，1已读',
          `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除，0未删除，1已删除',
          `create_at` int(11) NOT NULL COMMENT '创建时间',
          `update_at` int(11) NOT NULL COMMENT '更新时间',
          `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '通知的用户Id',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息记录表';
        
        
        CREATE TABLE `paragraph` (
          `paragraphId` int(11) NOT NULL AUTO_INCREMENT COMMENT '段落ID',
          `articleId` int(11) NOT NULL COMMENT '内容ID',
          `orderId` int(11) NOT NULL DEFAULT '0' COMMENT '段落顺序',
          `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (`paragraphId`),
          KEY `articleId` (`articleId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章段落表';
        
        
        CREATE TABLE `paragraph_content` (
          `contentId` int(11) NOT NULL AUTO_INCREMENT COMMENT '内容ID',
          `articleId` int(11) NOT NULL COMMENT '文章ID',
          `paragraphId` int(11) NOT NULL COMMENT '段落ID',
          `type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text' COMMENT '内容类型',
          `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
          `orderId` int(11) NOT NULL DEFAULT '0' COMMENT '内容顺序',
          `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (`contentId`),
          KEY `articleId` (`articleId`),
          KEY `paragraphId` (`paragraphId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章段落内容表';
        
        
        CREATE TABLE `user` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户Id',
          `nickName` varbinary(255) DEFAULT NULL COMMENT '用户昵称',
          `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户类型 1:普通用户 2:媒体用户',
          `avatarUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '用户头像图片的 URL',
          `gender` tinyint(4) DEFAULT NULL COMMENT '性别（0 未知 1 男性 2 女性）',
          `country` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户所在国家',
          `province` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户所在省份',
          `city` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户所在城市',
          `language` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '语言（en 英文 zh_CN 简体中文 zh_TW 繁体中文）',
          `birthday` datetime DEFAULT NULL COMMENT '生日',
          `signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户签名',
          `created_at` int(11) NOT NULL COMMENT '创建时间',
          `updated_at` int(11) NOT NULL COMMENT '更新时间',
          `session_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `openid` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `unionid` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181018_034015_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181018_034015_init cannot be reverted.\n";

        return false;
    }
    */
}
