<?php

use yii\db\Migration;

/**
 * Class m181022_063723_tag
 */
class m181022_063723_tag extends Migration
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
        
        create table `tag` (
  `tagName` varbinary(255) COMMENT '标签名称',
  `headImg` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头图',
  PRIMARY KEY (`tagName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181022_063723_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181022_063723_tag cannot be reverted.\n";

        return false;
    }
    */
}
