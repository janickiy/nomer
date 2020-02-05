<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vk`.
 */
class m170328_142237_create_vk_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('vk', [
            'id' => $this->primaryKey(),
		    'www' => $this->string(),
            'skype' => $this->string(),
            'instagram' => $this->string(),
            'twitter' => $this->string(),
            'facebook' => $this->string(),
            'phone1' => $this->string(),
            'phone2' => $this->string(),
            'phone3' => $this->string(),
            'phone4' => $this->string(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('vk');
    }
}
