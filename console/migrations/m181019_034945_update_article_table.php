<?php

use yii\db\Migration;

/**
 * Class m181019_034945_update_article_table
 */
class m181019_034945_update_article_table extends Migration
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
        ADD `coverType` set('image','video') COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'image' COMMENT '封面类型' AFTER `headImg`;
        
        update article set covers = JSON_ARRAY(headImg) where headImg is not null and covers is null;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181019_034945_update_article_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181019_034945_update_article_table cannot be reverted.\n";

        return false;
    }
    */
}
