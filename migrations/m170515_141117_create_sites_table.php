<?php

use yii\db\Migration;

/**
 * Handles the creation of table `sites`.
 */
class m170515_141117_create_sites_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('sites', [
            'id'        => $this->primaryKey(),
            'name'      => $this->string(),
            'vk_id'     => $this->string(),
            'vk_secret' => $this->string(),
            'fb_id'     => $this->string(),
            'fb_secret' => $this->string(),
            'gg_id'     => $this->string(),
            'gg_secret' => $this->string(),
            'metrika'   => $this->text(),
            'analytics' => $this->text(),
            'is_demo'   => $this->boolean()->defaultValue(false)
        ]);

        $this->insert("sites", [
            'name'      => 'nomer.io',
            'vk_id'     => '6003888',
            'vk_secret' => 'FJP4dBgt9nNoHobaoCuP',
            'fb_id'     => '111376575615479',
            'fb_secret' => '872f80a004aded27370c629331e25d17',
            'gg_id'     => '386713275441-pnfm2jjneeaveamci0sj7moe076n0td6.apps.googleusercontent.com',
            'gg_secret' => 'EaNe6T_tWn6dNErJ8NAPZ_Sh',
            'metrika'   => '',
            'analytics' => '<script>(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');ga(\'create\', \'UA-96815159-1\', \'auto\');ga(\'send\', \'pageview\');</script>'
        ]);

        $this->insert("sites", [
            'name'      => 'zagadok.net',
            'vk_id'     => '6018017',
            'vk_secret' => 'Eci3odgYYyZTiZW5K0Vf',
            'fb_id'     => '624551517738214',
            'fb_secret' => 'c6e28877f5cc2b93b2fec3fd67b39a7f',
            'gg_id'     => '386713275441-pnfm2jjneeaveamci0sj7moe076n0td6.apps.googleusercontent.com',
            'gg_secret' => 'EaNe6T_tWn6dNErJ8NAPZ_Sh',
            'metrika'   => '',
            'analytics' => '<script>(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');ga(\'create\', \'UA-98601031-1\', \'auto\');ga(\'send\', \'pageview\');</script>'
        ]);

        $this->insert("sites", [
            'name'      => 'zapalil.com',
            'fb_id'     => '1772125642814426',
            'fb_secret' => '2c25ca09b5477f5ce7d51f33c002cd53',
            'gg_id'     => '386713275441-pnfm2jjneeaveamci0sj7moe076n0td6.apps.googleusercontent.com',
            'gg_secret' => 'EaNe6T_tWn6dNErJ8NAPZ_Sh',
        ]);

        $this->addColumn(\app\models\SearchRequest::tableName(), 'site_id', $this->smallInteger());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('sites');
    }
}
