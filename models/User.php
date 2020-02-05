<?php

namespace app\models;

use app\components\CostsHelper;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $uuid
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string $code
 * @property string $auth_key
 * @property integer $balance
 * @property string $tm_create
 * @property string $tm_update
 * @property string $tm_last_auth
 * @property boolean $is_admin
 * @property boolean $is_vip
 * @property integer $plan
 * @property string $checks
 * @property string $ip
 * @property int $status
 * @property int $ban
 * @property boolean $is_confirm
 * @property string $tm_confirm
 * @property UserContact[] $contacts
 * @property Payment[] $payments
 * @property integer $geo_id
 * @property Geo $geo
 * @property integer $ref_checks
 * @property integer $ref_id
 * @property double $ref_balance
 * @property Repost $repost
 * @property string $comment
 * @property string $token
 */
class User extends ActiveRecord implements IdentityInterface
{
    const BAN_IP = 1;
    const BAN_EVERCOOKIE = 2;
    const BAN_FINGERPRINT = 3;
    const BAN_PHONE = 4;

    public static function primaryKey()
    {
        return ["id"];
    }

    public static function getBanStatusText($status)
    {
        switch ($status) {
            case self::BAN_IP: return 'IP';
            case self::BAN_EVERCOOKIE: return 'Evercookie';
            case self::BAN_FINGERPRINT: return 'Fingerprint';
            case self::BAN_PHONE: return 'Номер телефона';
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tm_create', 'tm_update', 'tm_last_auth', 'tm_confirm'], 'safe'],
            [['phone', 'code', 'auth_key', 'password_reset_token', 'ip', 'comment', 'email', 'token'], 'string', 'max' => 255],
            [['balance', 'ref_checks', 'ref_balance'], 'number'],
            [['plan', 'checks', 'status', 'ref_id'], 'integer'],
            [['is_admin', 'is_test', 'is_vip', 'is_confirm'], 'boolean'],
            [['comment'], 'filter', 'filter'=> '\yii\helpers\HtmlPurifier::process'],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['tm_create'],
                ],
                'value' => new Expression('NOW()'),
            ],
            'ip' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ip'],
                ],
                'value' => Yii::$app->request->isConsoleRequest?"":\Yii::$app->request->getUserIP(),
            ],
            'checks' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['checks'],
                ],
                'value' => 0,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'phone'         => 'Телефон',
            'code'          => 'Код',
            'auth_key'      => 'Секретный ключ',
            'balance'       => 'Баланс',
            'tm_create'     => 'Дата регистрации',
            'tm_update'     => 'Дата обновления',
            'tm_last_auth'  => 'Последний вход',
            'is_admin'      => 'Админ',
            'is_vip'        => 'VIP',
            'checks'        => 'Проверки',
            'ban'           => 'Статус',
            'comment'       => 'Комментарий'
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        $identity = self::findOne($id);
        if($identity) {
            $ip = UserIp::find()->where(["user_id" => $id, "ip" => \Yii::$app->request->getUserIP()])->one();
            if(is_null($ip)) {
                $ip = new UserIp();
                $ip->user_id = $id;
                $ip->ip = \Yii::$app->request->getUserIP();
                $ip->tm = new Expression('NOW()');
                $ip->save();
            }
        }

        return $identity;
    }

    public static function findByCode($code)
    {
        return self::find()->where(["code" => $code])->one();
    }

    /**
     * @param $email
     * @return \app\models\User|null
     */
    public static function findByEmail($email)
    {
        return self::find()->where(["email" => mb_strtolower($email)])->one();
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::find()->where(new Expression("MD5(concat_ws('-', 'nomer', id, 'io')) = '".$token."'"))->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param  string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);

        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();

        \Yii::$app->mailer->compose()
            ->setTextBody("Для восстановления пароля перейдите по ссылке: ".Url::toRoute(['site/set-password', 'token' => $this->password_reset_token], true))
            ->setFrom('noreply@'.\Yii::$app->name)
            ->setTo($this->email)
            ->setSubject(\Yii::$app->name." - восстановление пароля")
            ->send();

        return $this;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return trim($this->password) === $password;
    }

    public function getRequests() {
        return $this->hasMany(SearchRequest::className(), ["user_id" => "id"]);
    }

    public function getAuth() {
        return $this->hasMany(Auth::className(), ["user_id" => "id"]);
    }

    public function getSettings() {
        return $this->hasMany(UserSetting::className(), ["user_id" => "id"])->indexBy("param");
    }

    public function getSetting($param, $defaultValue = null) {
        return isset($this->settings[$param])?ArrayHelper::getValue($this->settings[$param], "value"):$defaultValue;
    }

    public function getContacts()
    {
        return $this->hasMany(UserContact::className(), ['user_id' => 'id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['user_id' => 'id']);
    }

    public function getGeo()
    {
        return $this->hasOne(Geo::className(), ['geoname_id' => 'geo_id']);
    }

    public function getRepost()
    {
        return $this->hasOne(Repost::className(), ['user_id' => 'id']);
    }

    public function addBalance($sum, $amount, $balance = true, $siteID = 0)
    {
        $sum += $this->balance;
        $this->balance = 0;

        $cost = CostsHelper::getCost(1, $siteID);

        if($sum >= CostsHelper::getCostTotal(500, $siteID)) {
            $cost = CostsHelper::getCost(500, $siteID);
        } elseif($sum >= CostsHelper::getCostTotal(300, $siteID)) {
            $cost = CostsHelper::getCost(300, $siteID);
        } elseif($sum >= CostsHelper::getCostTotal(100, $siteID)) {
            $cost = CostsHelper::getCost(100, $siteID);
        } elseif($sum >= CostsHelper::getCostTotal(50, $siteID)) {
            $cost = CostsHelper::getCost(50, $siteID);
        } elseif($sum >= CostsHelper::getCostTotal(20, $siteID)) {
            $cost = CostsHelper::getCost(20, $siteID);
        } elseif($sum >= CostsHelper::getCostTotal(10, $siteID)) {
            $cost = CostsHelper::getCost(10, $siteID);
        }

        $checks = floor($sum / $cost);
        $rest = $sum - $checks * $cost;

        $this->checks += $checks;
        if ($balance) $this->balance += $rest;

        if ($this->save()) {
            if ($this->ref_id) {
                $refUser = User::find()->where(['id' => $this->ref_id])->one();
                $refUser->ref_balance += $amount * 0.1;
                $refUser->save();
            }
        }

        return true;
    }

    public function getSubs() {
        return $this->hasMany(UserSub::className(), ["user_id" => "id"]);
    }
    /**
     * @return string
     * @throws \Exception
     */
    public function generateLink()
    {
        $code = implode(array_map(function($char) {
            return rand(0, 1) ? strtoupper($char) : $char;
        }, str_split(bin2hex(random_bytes(4)))));

        $link = new Link();
        $link->user_id = $this->id;
        $link->code = $code;

        try {
            $link->save();
        } catch (\Exception $e) {
            if ($e->getCode() == 23505) return $this->generateLink();
            throw $e;
        }

        return 'https://tels.gg/c/' . $code;
    }
}
