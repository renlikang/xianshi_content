<?php

use yii\db\Migration;

/**
 * Class m181031_093116_update_user
 */
class m181031_093116_update_user extends Migration
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
            alter table user add `status` tinyint(1) not null DEFAULT 1 COMMENT '用户状态:1正常，2禁言' after unionid;
            alter table user add `deleteFlag` tinyint(1) not null DEFAULT 0 COMMENT '删除标识:0正常，1删除' after status;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181031_093116_update_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181031_093116_update_user cannot be reverted.\n";

        return false;
    }
    */
}
