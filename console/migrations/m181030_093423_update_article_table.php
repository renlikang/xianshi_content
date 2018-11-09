<?php

use yii\db\Migration;

/**
 * Class m181030_093423_update_article_table
 */
class m181030_093423_update_article_table extends Migration
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
        ADD `source` varchar(32) NULL COMMENT '文章来源' AFTER `type`,
        CHANGE `type` `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '文章类型，1 PGC 2 爬虫 3 UGC' AFTER `authorId`,
        CHANGE `status` `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:启用 0:禁用' AFTER `orderId`;
        update article set source = 'instagram' where type = 2;
        ");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181030_093423_update_article_table cannot be reverted.\n";

        return false;
    }
    */
}
