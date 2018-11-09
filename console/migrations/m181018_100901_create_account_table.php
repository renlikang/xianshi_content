<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account`.
 */
class m181018_100901_create_account_table extends Migration
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

        CREATE TABLE `account` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '账户Id',
          `userId` int(11) NOT NULL COMMENT '用户Id',
          `decibels` int(11) NOT NULL COMMENT '分贝账户',
          PRIMARY KEY (`id`),
          UNIQUE KEY `userId` (`userId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='账户表';
        
        CREATE TABLE `account_logs` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录Id',
          `accountId` int(11) NOT NULL COMMENT '账户Id',
          `log` json NOT NULL COMMENT '记录内容',
          `cTime` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
          `uTIme` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          PRIMARY KEY (`id`),
          KEY `accountId` (`accountId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='账户变动记录表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('account_logs');
        $this->dropTable('account');
    }
}
