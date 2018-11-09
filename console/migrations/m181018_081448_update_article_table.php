<?php

use yii\db\Migration;

/**
 * Class m181018_081448_update_article_table
 */
class m181018_081448_update_article_table extends Migration
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
        ADD `covers` json NULL COMMENT '封面图片' AFTER `headImg`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181018_081448_update_article_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181018_081448_update_article_table cannot be reverted.\n";

        return false;
    }
    */
}
