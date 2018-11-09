<?php

use yii\db\Migration;

/**
 * Class m181022_030845_update_account_logs_table
 */
class m181022_030845_update_account_logs_table extends Migration
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
        
        ALTER TABLE `account_logs`
        CHANGE `cTime` `cTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间' AFTER `log`,
        CHANGE `uTIme` `uTime` timestamp NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间' AFTER `cTime`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181022_030845_update_account_logs_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181022_030845_update_account_logs_table cannot be reverted.\n";

        return false;
    }
    */
}
