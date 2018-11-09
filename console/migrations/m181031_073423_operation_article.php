<?php

use yii\db\Migration;

/**
 * Class m181031_073423_operation_article
 */
class m181031_073423_operation_article extends Migration
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
            create table operation_article(
              aid int(11) NOT NULL COMMENT '管理者ID',
              articleId int(11) NOT NULL COMMENT '文章ID',
              cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
              uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
              PRIMARY KEY (aid, articleId)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '后台操作员-文章映射表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181031_073423_operation_article cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181031_073423_operation_article cannot be reverted.\n";

        return false;
    }
    */
}
