<?php

use yii\db\Migration;

/**
 * Class m181018_035502_admin_init
 */
class m181018_035502_admin_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->db = 'db_admin';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        SET NAMES utf8mb4;
        
        CREATE TABLE `admin` (
        `aid` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理者ID',
        `username` varchar(64) NOT NULL COMMENT '用户名',
        `password` varchar(255) NOT NULL COMMENT '密码',
        `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:启用 0:禁用',
        `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
        `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
        `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
        `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:正常账户 2:授权账户',
        PRIMARY KEY (`aid`),
        UNIQUE KEY `username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户登录表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181018_035502_admin_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181018_035502_admin_init cannot be reverted.\n";

        return false;
    }
    */
}
