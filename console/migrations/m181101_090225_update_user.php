<?php

use yii\db\Migration;

/**
 * Class m181101_090225_update_user
 */
class m181101_090225_update_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181101_090225_update_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181101_090225_update_user cannot be reverted.\n";

        return false;
    }
    */
}
