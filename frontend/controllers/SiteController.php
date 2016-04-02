<?php

namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\base\Event;

// 定义事件的关联数据
class MsgEvent extends Event {

    public $dateTime;   // 微博发出的时间
    public $author;     // 微博的作者
    public $content;    // 微博的内容

}

/**
 * Site controller
 */
class SiteController extends Controller {

    const EVENT_BEFOR_INDEX = 'beforIndex';
    const EVENT_AFTER_INDEX = 'afterIndex';

    public function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        $this->on(self::EVENT_BEFOR_INDEX, [$this, 'sayHello']);
        $this->on(self::EVENT_BEFOR_INDEX, [$this, 'sayJ8']);
        $this->on(self::EVENT_BEFOR_INDEX, [$this, 'sayOK']);
        $this->on(self::EVENT_AFTER_INDEX, [$this, 'sayAfterOK']);
        $this->on(self::EVENT_AFTER_INDEX, [$this, 'sayAfterJ8']);
        $this->on(self::EVENT_AFTER_INDEX, [$this, 'sayAfterHello']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
//        $event = new MsgEvent;
//        $event->author = 'duanLuJian';
//        $event->dateTime = time();
//        $event->content = 'hello, world!';
//        $this->trigger(self::EVENT_BEFOR_INDEX,$event);
//        echo 'index start …………';
//        echo '<br>';
//        $this->trigger(self::EVENT_AFTER_INDEX);
//        die;
//        var_dump(Yii::getAlias('@webroot'));
//        var_dump(Yii::getAlias('@web'));
//        echo '<pre>';
//        var_dump(\Yii::$app->user);die;
//        var_dump(\ReflectionClass::export(\Yii::$app->user));die;
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout() {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup() {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
                    'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset() {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
                    'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
                    'model' => $model,
        ]);
    }

    public function sayHello(Event $event) {
        echo 'Hello,'.$event->author.'--'.  date('Y-m-d H:i:s',$event->dateTime).'frggf';
        echo '<hr>';
    }

    public function sayJ8() {
        echo 'J8<hr>';
    }

    public function sayOK() {
        echo 'OK<hr>';
    }

    public function sayAfterHello() {
        echo 'AfterHello<hr>';
    }

    public function sayAfterJ8() {
        echo 'AfterJ8<hr>';
    }

    public function sayAfterOK() {
        echo 'AfterOK<hr>';
    }

}
