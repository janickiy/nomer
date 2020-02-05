<?php

use yii\db\Migration;

/**
 * Handles the creation of table `facebook`.
 */
class m170210_113137_create_facebook_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('facebook', [
            'id' => $this->primaryKey(),
            'tm' => $this->dateTime(),
            'phone' => $this->string(),
            'fb_id' => $this->string(),
            'name' => $this->string(),
            'photo' => $this->text()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('facebook');
    }
}
