<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_fake_praise`.
 */
class m181031_130212_create_article_fake_praise_table extends Migration
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
            CREATE TABLE `article_fake_praise` (
              `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '伪造点赞Id',
              `articleId` int(11) NOT NULL COMMENT '文章Id',
              `fakePraise` int(11) NOT NULL COMMENT '伪造点赞数',
              `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `uTime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
              PRIMARY KEY (`id`),
              UNIQUE KEY `articleId` (`articleId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='伪造点赞表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('article_fake_praise');
    }
}
