<?php

use yii\db\Migration;

/**
 * Class m181031_031553_role_admin
 */
class m181031_031553_role_admin extends Migration
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
            create table role_admin(
              roleName varchar(255) not null comment '角色名称',
              aid int(11) NOT NULL COMMENT '管理者ID',
              PRIMARY KEY (roleName, aid)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '后台角色-后台用户映射表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181031_031553_role_admin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181031_031553_role_admin cannot be reverted.\n";

        return false;
    }
    */
}
