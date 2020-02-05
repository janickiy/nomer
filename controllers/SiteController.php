<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\Link;
use app\models\NewPasswordForm;
use app\models\PhoneRequest;
use app\models\RemindForm;
use app\models\SigninForm;
use app\models\SignupForm;
use app\models\Ticket;
use app\models\User;
use app\models\UserEvercookie;
use app\models\UserFingerprint;
use app\models\UserSetting;
use app\models\UserTest;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use app\components\AuthHandler;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SiteController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['signin', 'signup', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['signin', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionApi()
    {
        echo "Превед медвед! ;)";
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * Displays homepage.
     */
    public function actionIndex()
    {
        $model = new PhoneRequest();
        if(\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(["pay/find-phone", "id" => $model->id]);
        }

        return $this->render('index', [
            "model" => $model
        ]);
    }

    public function actionFindPhone()
    {
        $model = new PhoneRequest();
        if($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(["pay/find-phone", "id" => $model->id]);
        }

        return $this->render('find-phone', [
            "model" => $model
        ]);
    }

    public function actionIos()
    {
        return $this->render('ios');
    }

    public function actionSignin()
    {
        $signinForm = new SigninForm();
        if(\Yii::$app->request->isAjax && $signinForm->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($signinForm);
        }

        if(\Yii::$app->request->isPost && $signinForm->load(\Yii::$app->request->post())) {
            if($signinForm->validate() && $signinForm->login()) {
                return $this->goBack();
            }
        }

        return $this->render('signin', [
            "signinForm" => $signinForm
        ]);
    }

    public function actionSignup()
    {
        $signupForm = new SignupForm();
        if(\Yii::$app->request->isAjax && $signupForm->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($signupForm);
        }

        if(\Yii::$app->request->isPost && $signupForm->load(\Yii::$app->request->post())) {
            if($signupForm->validate()) {
                $user = $signupForm->createUser();
                if(\Yii::$app->getUser()->login($user, 3600 * 24 * 30)) {
                    $site = \app\models\Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();
                    $log = new \app\models\UserAuthLog();
                    $log->user_id = $user->id;
                    $log->site_id = \yii\helpers\ArrayHelper::getValue($site, "id", 0);
                    $log->ip = \Yii::$app->request->getUserIP();
                    $log->tm = new \yii\db\Expression('NOW()');
                    $log->save();
                }
                return $this->goBack();
            }
        }

        return $this->render('signup', [
            "signupForm" => $signupForm,
        ]);
    }

    public function actionSetPassword($token = "")
    {
        /*
        $password = \Yii::$app->request->post('password');
        $re_password = \Yii::$app->request->post('re-password');
        */

        if (empty($token)) {
            return $this->goHome();
        }

        /** @var User $user */
        $user = User::findOne(['password_reset_token' => $token]);

        if (empty($user)) {
            return $this->goHome();
        }

        /*
        if (!empty($password) && !empty($re_password)) {
            if ($password === $re_password) {
                $user->removePasswordResetToken();
                $user->setPassword($password);

                $user->save();

                \Yii::$app->user->login($user, 3600 * 24 * 30);

                return $this->redirect(['/cabinet/stats/index']);
            }

            return $this->render('new_password', ['error' => 'Пароли не совпадают']);
        }
        */

        $model = new NewPasswordForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $user->password = $model->password;
            $user->password_reset_token = '';
            if($user->save()) {
                \Yii::$app->user->login($user, 3600 * 24 * 30);

                return $this->goHome();
            }
        }

        return $this->render('new_password', [
            "model" => $model
        ]);
    }

    public function actionRemind()
    {
        $remindForm = new RemindForm();
        if(\Yii::$app->request->isPost && $remindForm->load(\Yii::$app->request->post())) {
            if($remindForm->validate()) {
                $remindForm->remind();
                \Yii::$app->session->setFlash("remindMessage", "Ссылка для восстановления пароля отправлена на указанный E-mail");
                return $this->refresh();
            }
        }

        return $this->render('remind', [
            "remindForm" => $remindForm,
        ]);
    }

    public function actionConfirm()
    {
        $user = false;
        $token = \Yii::$app->request->get("token", null);
        if($token) {
            $user = User::find()->where(new Expression("MD5('cc-' || id) = '".$token."'"))->one();
            if($user) {
                $user->is_confirm = true;
                $user->tm_confirm = new Expression("NOW()");
                $user->save();
            }
        }
        if(!$user) return "";

        return $this->render("confirm", [
            "user" => $user
        ]);
    }

    public function actionFree()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var $user \app\models\User */
        $user = \Yii::$app->getUser()->getIdentity();
        if($user->is_confirm && !$user->is_test) {
            $user->checks += 5;
            $user->is_test = true;
            if($user->save()) {
                $test = new UserTest();
                $test->user_id = $user->id;
                $test->tm = new Expression('NOW()');
                $test->ip = \Yii::$app->request->getUserIP();
                return ["success" => 1, "checks" => $user->checks];
            }
        }
        return ["success" => 0];
    }

    public function actionSendConfirm()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        /* @var $user \yii\web\View */
        $user = \Yii::$app->getUser()->getIdentity();

        return \Yii::$app->mailer->compose()
            ->setTextBody("Для подтверждения e-mail адреса перейдите по ссылке: ".Url::toRoute(['site/confirm', 'token' => md5("cc-".$user->id)], true))
            ->setFrom('noreply@'.\Yii::$app->name)
            ->setTo($user->email)
            ->setSubject(\Yii::$app->name." - подтверждение e-mail адреса")
            ->send();
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

    public function actionLogout()
    {
        \Yii::$app->getUser()->logout();
        return $this->goHome();
    }

    public function actionRedirect($phone) {
        $phone = preg_replace('/\D/', '', $phone);
        if(mb_strlen($phone) == 10) {
            $phone = "8".$phone;
        } elseif(mb_strlen($phone) == 11 && $phone{0} == 7) {
            $phone = preg_replace('/^7/', '8', $phone);
        }
        if(preg_match('/^8(\d{10})$/', $phone)) {
            return $this->redirect(["result/index", "phone" => $phone]);
        } else {
            \Yii::$app->session->setFlash("error", "Номер $phone указан не корректно!");
            return $this->goHome();
        }
    }

    public function actionImage($uuid) {
        $response = \Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;

        if ( !is_resource($response->stream = @fopen("http://storage.aprokat.com/nomerio/".$uuid, "r")) ) {
            //throw new \yii\web\ServerErrorHttpException('file access failed: permission deny');
            $response->stream = @fopen(\Yii::getAlias('@webroot').'/img/nophoto.png', "r");
        }
        $response->send();
    }

    public function actionTest()
    {
        return $this->render("test");
    }

    public function actionFingerprint($hash) {
        \Yii::$app->response->format = Response::FORMAT_RAW;
        if(!\Yii::$app->getUser()->isGuest) {
            $fp = UserFingerprint::find()->where(["user_id" => \Yii::$app->getUser()->getId(), "hash" => $hash, "ip" => \Yii::$app->request->getUserIP()])->one();
            if(is_null($fp)) {
                $fp = new UserFingerprint();
                $fp->user_id = \Yii::$app->getUser()->getId();
                $fp->hash = $hash;
                $fp->ip = \Yii::$app->request->getUserIP();
                $fp->tm = new Expression('NOW()');
                $fp->save();
            }

            $user = \Yii::$app->getUser()->getIdentity();

            $hashes = ArrayHelper::getColumn(UserFingerprint::find()->where(["user_id" => \Yii::$app->getUser()->getId()])->all(), "hash");
            $checks = UserFingerprint::find()->where(["<>", "user_id", \Yii::$app->getUser()->getId()])->andWhere(["hash" => $hashes])->all();
            /*
            if(count($checks)) {
                $user->status = 0;
                $user->ban = User::BAN_FINGERPRINT;
                $user->save();
            }
            */
        }
        \Yii::$app->response->headers->add('Content-Type', 'image/gif');
        return "\x47\x49\x46\x38\x39\x61\x1\x0\x1\x0\x80\x0\x0\xff\xff\xff\xff\xff\xff\x21\xf9\x04\x1\x0a\x0\x1\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x4c\x1\x0\x3b";
    }

    public function actionEvercookie($hash) {
        \Yii::$app->response->format = Response::FORMAT_RAW;
        if(!\Yii::$app->getUser()->isGuest) {
            $ec = UserEvercookie::find()->where(["user_id" => \Yii::$app->getUser()->getId(), "data" => $hash, "ip" => \Yii::$app->request->getUserIP()])->one();
            if(is_null($ec)) {
                $ec = new UserEvercookie();
                $ec->user_id = \Yii::$app->getUser()->getId();
                $ec->data = $hash;
                $ec->ip = \Yii::$app->request->getUserIP();
                $ec->tm = new Expression('NOW()');
                $ec->save();
            }
            $originalUser = User::find()->where(new Expression("MD5(CONCAT_WS('-', 'nomerio', id)) = '".$hash."'"))->one();
            if($originalUser && ($originalUser->id != \Yii::$app->getUser()->getId() && $originalUser->is_test)) {
                $user = User::find()->where(["id" => \Yii::$app->getUser()->getId()])->one();
                if($user->status == 1 && $user->is_test){
                    $user->status = 0;
                    $user->ban = User::BAN_EVERCOOKIE;
                    $user->save();
                }
            }
        }
        \Yii::$app->response->headers->add('Content-Type', 'image/gif');
        return "\x47\x49\x46\x38\x39\x61\x1\x0\x1\x0\x80\x0\x0\xff\xff\xff\xff\xff\xff\x21\xf9\x04\x1\x0a\x0\x1\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x4c\x1\x0\x3b";
    }

    public function actionSetSetting() {
        $param = \Yii::$app->request->get("param");
        $value = \Yii::$app->request->get("value");
        if(\Yii::$app->request->isAjax && !\Yii::$app->getUser()->isGuest) {
            $s = UserSetting::find()->where(["user_id" => \Yii::$app->getUser()->getId(), "param" => $param])->one();
            if(is_null($s)) {
                $s = new UserSetting();
                $s->user_id = \Yii::$app->getUser()->getId();
                $s->param = $param;
            }
            $s->value = $value;
            return $s->save();
        }
        return false;
    }

    public function actionContacts() {
        /*
        $model = new Ticket();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            //return $this->redirect(['site/contacts']);
        }

        /*
        if((\Yii::$app->request->isAjax == false) && \Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash("success", "Запрос успешно создан!");
            return $this->redirect(['site/contacts']);
        }
        */
/*
        $ticketsDataProvider = new ActiveDataProvider([
            'query' => Ticket::find()->where(["user_id" => \Yii::$app->getUser()->getId()])
        ]);

        return $this->render("contacts", [
            "model" => $model,
            "ticketsDataProvider" => $ticketsDataProvider
        ]);
*/
    }

    public function actionCode($code)
    {
        $link = Link::find()->where(compact('code'))->one();

        if (!$link || !$link->user || !$link->user->repost) throw new NotFoundHttpException();

        if (strtotime($link->tm) < strtotime('-7 day')) {
            throw new NotFoundHttpException();
        }

        $response = file_get_contents('https://api.vk.com/method/users.get?user_id=' . $link->user->repost->vk_id . '&v=5.65&lang=ru&fields=photo_max_orig&access_token=8f95fab19fb8d3d41bdeeb28f0112cb2cd3c86a93fc66acbd29f327d1aa3f196540bfe10dcd4ca97baf37');
        $response = Json::decode($response);
        $user = ArrayHelper::getValue($response, ['response', 0]);

        if (!$user) throw new NotFoundHttpException();

        return $this->render('user', compact('user'));
    }
}
