<?php

use yii\db\Migration;

/**
 * Class m181022_095442_update_article_table
 */
class m181022_095442_update_article_table extends Migration
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
        CHANGE `coverType` `coverType` set('image','video','audio') COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'image' COMMENT '封面类型' AFTER `headImg`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181022_095442_update_article_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181022_095442_update_article_table cannot be reverted.\n";

        return false;
    }
    */
}
