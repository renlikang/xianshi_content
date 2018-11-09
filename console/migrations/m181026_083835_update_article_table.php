<?php

use yii\db\Migration;

/**
 * Class m181026_083835_update_article_table
 */
class m181026_083835_update_article_table extends Migration
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
        
        ALTER TABLE `article`
        CHANGE `type` `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '文章类型，1 微信文章 2 Ins 3 UGC' AFTER `authorId`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181026_083835_update_article_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181026_083835_update_article_table cannot be reverted.\n";

        return false;
    }
    */
}
