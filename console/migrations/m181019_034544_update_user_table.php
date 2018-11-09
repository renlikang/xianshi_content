<?php

use yii\db\Migration;

/**
 * Class m181019_034544_update_user_table
 */
class m181019_034544_update_user_table extends Migration
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
        
        ALTER TABLE `user`
        CHANGE `signature` `signature` varbinary(255) NULL COMMENT '用户签名' AFTER `birthday`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181019_034544_update_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181019_034544_update_user_table cannot be reverted.\n";

        return false;
    }
    */
}
