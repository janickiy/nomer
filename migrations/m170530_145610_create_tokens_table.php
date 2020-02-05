<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tokens`.
 */
class m170530_145610_create_tokens_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tokens', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger(),
            'token' => $this->string(),
            'status' => $this->smallInteger()->defaultValue(1),
            'tm_ban' => $this->timestamp()
        ]);

        $this->insert('tokens', [
            'type' => 1,
            'token' => 'ErqH2RfLL_X2UubBtc_jt8VKF3cXtsic'
        ]);

        $this->insert('tokens', [
            'type' => 1,
            'token' => 'bGJ6WkDMnFa28s8ndi4eOe57H3cXw09r'
        ]);

        $this->insert('tokens', [
            'type' => 1,
            'token' => 'HM~J_5AbOT1lQAt_XB9Ryol353cXxT15'
        ]);

        $this->insert('tokens', [
            'type' => 1,
            'token' => '-eMqbxzUV1P-SK_Grs9z5AJI43cYB6U2'
        ]);

        $this->insert('tokens', [
            'type' => 1,
            'token' => 'RRgxoy2HdIMC4Rg2S2SOWLruT3cYB~He'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tokens');
    }
}
