<?php

use yii\db\Migration;

/**
 * Class m181031_030051_role
 */
class m181031_030051_role extends Migration
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
        
        create table file_info(
          `fileId` varchar(255) NOT NULL COMMENT '文件md5标识',
          `path` varchar(255) not null comment 'path地址',
          `sourceType` int(11) not null default 1 comment '资源服务器供应商， 1:又拍云',
          `url` text COMMENT 'url地址',
          `fileSize`  int(11) not null default 0 comment '文件大小',
          cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          PRIMARY KEY (fileId)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '文件信息表';
        
        create table role(
          roleName varchar(255) not null comment '角色名称',
          cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
          uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
          deleteFlag tinyint(1) not null DEFAULT 0 COMMENT '删除标识:0正常，1删除',
          PRIMARY KEY (roleName)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '后台角色表';        
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181031_030051_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181031_030051_role cannot be reverted.\n";

        return false;
    }
    */
}
