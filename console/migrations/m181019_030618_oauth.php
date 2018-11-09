<?php

use yii\db\Migration;

/**
 * Class m181019_030618_oauth
 */
class m181019_030618_oauth extends Migration
{
    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        SET NAMES utf8mb4;
        
        CREATE TABLE `oauth_clients` (
  `client_id` varchar(32) NOT NULL,
  `client_secret` varchar(32) DEFAULT NULL,
  `redirect_uri` varchar(1000) NOT NULL,
  `grant_types` varchar(100) NOT NULL,
  `scope` varchar(2000) DEFAULT NULL,
  `access_token_expires` int(10) NOT NULL DEFAULT '28800',
  `refresh_token_expires` int(10) NOT NULL DEFAULT '86400',
  `user_id` int(11) DEFAULT NULL,
  `logout_uri` varchar(1000) NOT NULL DEFAULT '' COMMENT '登出回跳地址',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `oauth_scopes` (
  `scope` varchar(2000) NOT NULL,
  `is_default` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181019_030618_oauth cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181019_030618_oauth cannot be reverted.\n";

        return false;
    }
    */
}
