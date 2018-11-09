<?php

use yii\db\Migration;

/**
 * Class m181102_105957_update_account_table
 */
class m181102_105957_update_account_table extends Migration
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
            ALTER TABLE `account`
            CHANGE `decibels` `decibels` int(11) NOT NULL DEFAULT '0' COMMENT '分贝账户' AFTER `userId`,
            ADD `superPills` int(11) NOT NULL DEFAULT '0' COMMENT '特殊药丸账户';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181102_105957_update_account_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181102_105957_update_account_table cannot be reverted.\n";

        return false;
    }
    */
}
