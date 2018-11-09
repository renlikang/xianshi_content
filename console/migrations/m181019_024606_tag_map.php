<?php

use yii\db\Migration;

/**
 * Class m181019_024606_tag_map
 */
class m181019_024606_tag_map extends Migration
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
        
        create table `tag_map` (
  `tagName` varbinary(255) COMMENT '标签名称',
  `mapId` int(11) not null comment '映射ID',
  `mapType` tinyint(1) not null default  1 comment '类型 1：文章  2：用户的标签 3：用户喜欢的话题',
  PRIMARY KEY (`tagName`, `mapId`, `mapType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签映射表';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181019_024606_tag_map cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181019_024606_tag_map cannot be reverted.\n";

        return false;
    }
    */
}
