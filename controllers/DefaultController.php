<?php

namespace panix\mod\user\controllers;

use panix\engine\controllers\WebController;
use panix\mod\user\models\forms\ChangePasswordForm;
use panix\mod\user\models\forms\ForgotForm;
use panix\mod\user\models\forms\LoginForm;
use panix\mod\user\models\User;
use panix\mod\user\models\UserKey;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Default controller for User module
 */
class DefaultController extends WebController
{

    /**
     * @inheritdoc
     */
    /*public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'allowActions' => [
                    '*',
                    // The actions listed here will be allowed to everyone including guests.
                ]
            ],
        ];
    }*/

    /**
     * Display index - debug page, login page, or account page
     *
     * @return string|Response
     */
    public function actionIndex()
    {
        if (defined('YII_DEBUG') && YII_DEBUG) {
            $actions = Yii::$app->getModule("user")->getActions();
            return $this->render('index', ["actions" => $actions]);
        } elseif (Yii::$app->user->isGuest) {
            return $this->redirect(["/user/login"]);
        } else {
            return $this->redirect(["/user/account"]);
        }
    }

    /**
     * Display login page
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        $config = Yii::$app->settings->get('user');
        if (Yii::$app->user->isGuest) {
            // load post data and login
            $model = new LoginForm();
            $this->pageName = Yii::t('user/default', 'LOGIN');
            if ($model->load(Yii::$app->request->post()) && $model->login($config->login_duration * 86400)) {
                return $this->goBack(Yii::$app->getModule("user")->loginRedirect);
            }

            // render
            return $this->render('login', [
                'model' => $model,
            ]);
        } else {
            return $this->redirect(['/']);
        }
    }

    /**
     * Log user out and redirect
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        // redirect
        $logoutRedirect = Yii::$app->getModule("user")->logoutRedirect;
        if ($logoutRedirect === null) {
            return $this->goHome();
        } else {
            return $this->redirect($logoutRedirect);
        }
    }

    /**
     * Display registration page
     *
     * @return array|string|Response
     */
    public function actionRegister()
    {
        $config = Yii::$app->settings->get('user');
        if ($config->enable_register) {
            // set up new user/profile objects
            $user = Yii::$app->getModule("user")->model("User", ["scenario" => "register"]);
            $this->pageName = Yii::t('user/default', 'REGISTER');
            $this->breadcrumbs[] = $this->pageName;
            // load post data
            $post = Yii::$app->request->post();


            /*$mailer = Yii::$app->mailer;
            $subject = Yii::t("user/default", "👍 😀 ⚠  🛒  🔑 🔔 🏆 🎁 🎉 🤝 👉 Email Confirmation");
            $message = $mailer->compose(['html'=>'admin.tpl'], ['test'=>'dsa'])
                ->setTo('dev@pixelion.com.ua')
                ->setSubject($subject);
            $message->send();*/


            if ($user->load($post)) {


                // validate for ajax request
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($user);
                }

                // validate for normal request
                if ($user->validate()) {

                    // perform registration
                    $role = Yii::$app->getModule("user")->model("Role");
                    $user->setRegisterAttributes($role::ROLE_USER, Yii::$app->request->userIP)->save(false);
                    $this->afterRegister($user);

                    // set flash
                    // don't use $this->refresh() because user may automatically be logged in and get 403 forbidden
                    $successText = Yii::t("user/default", "Successfully registered [ {displayName} ]", ["displayName" => $user->getDisplayName()]);
                    $guestText = "";
                    if (Yii::$app->user->isGuest) {
                        $guestText = Yii::t("user/default", " - Please check your email to confirm your account");
                    }
                    Yii::$app->session->setFlash("register-success", $successText . $guestText);
                }
            }

            // render
            return $this->render("register", [
                'user' => $user,
            ]);
        } else {
            return $this->redirect(['/']);
        }

    }

    /**
     * Process data after registration
     *
     * @param \panix\mod\user\models\User $user
     */
    protected function afterRegister($user)
    {
        // determine userKey type to see if we need to send email
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $config = Yii::$app->settings->get('user');
        if ($user->status == $user::STATUS_INACTIVE) {
            $userKeyType = $userKey::TYPE_EMAIL_ACTIVATE;
        } elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            $userKeyType = $userKey::TYPE_EMAIL_CHANGE;
        } else {
            $userKeyType = null;
        }

        // check if we have a userKey type to process, or just log user in directly
        if ($userKeyType) {

            // generate userKey and send email
            $userKey = $userKey::generate($user->id, $userKeyType);
            if (!$numSent = $user->sendEmailConfirmation($userKey)) {

                // handle email error
                //Yii::$app->session->setFlash("Email-error", "Failed to send email");
            }
        } else {
            Yii::$app->user->login($user, $config->login_duration * 86400);
        }
    }

    /**
     * Confirm email
     */
    public function actionConfirm($key)
    {
        /** @var \panix\mod\user\models\UserKey $userKey */
        /** @var \panix\mod\user\models\User $user */
        // search for userKey
        $success = false;
        $userKey = Yii::$app->getModule("user")->model("UserKey");

        $userKey = $userKey::findActiveByKey($key, [$userKey::TYPE_EMAIL_ACTIVATE, $userKey::TYPE_EMAIL_CHANGE]);

        if ($userKey) {

            // confirm user
            $user = Yii::$app->getModule("user")->model("User");
            $user = $user::findOne($userKey->user_id);
            $user->confirm();

            // consume userKey and set success
            $userKey->consume();
            $success = $user->email;
        }

        // render
        return $this->render("confirm", [
            "userKey" => $userKey,
            "success" => $success
        ]);
    }

    /**
     * Account
     */
    public function actionAccount()
    {
        $user = Yii::$app->user->identity;
        $user->setScenario("account");
        $loadedPost = $user->load(Yii::$app->request->post());

        // validate for ajax request
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user);
        }

        // validate for normal request
        if ($loadedPost && $user->validate()) {

            // generate userKey and send email if user changed his email
            if (Yii::$app->getModule("user")->emailChangeConfirmation && $user->checkAndPrepEmailChange()) {

                $userKey = Yii::$app->getModule("user")->model("UserKey");
                $userKey = $userKey::generate($user->id, $userKey::TYPE_EMAIL_CHANGE);
                if (!$numSent = $user->sendEmailConfirmation($userKey)) {

                    // handle email error
                    //Yii::$app->session->setFlash("Email-error", "Failed to send email");
                }
            }

            // save, set flash, and refresh page
            $user->save(false);
            Yii::$app->session->setFlash("Account-success", Yii::t("user/default", "Account updated"));
            return $this->refresh();
        }

        // render
        return $this->render("account", [
            'user' => $user,
        ]);
    }

    /**
     * Profile
     */
    public function actionProfile()
    {
        if (!Yii::$app->user->identity)
            $this->error404();


        $this->pageName = Yii::t('user/default', 'PROFILE');
        $this->view->title = $this->pageName;
        $this->breadcrumbs[] = $this->pageName;

        //$user = Yii::$app->getModule("user")->model("User");
        $user = Yii::$app->user->identity;
        $loadedPost = $user->load(Yii::$app->request->post());


        // validate for normal request
        if ($loadedPost && $user->validate()) {
            $user->save(false);
            Yii::$app->session->setFlash("success", Yii::t("user/default", "Profile updated"));
            return $this->refresh();
        }


        $changePasswordForm = new ChangePasswordForm();
        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->validate()) {
            //$changePasswordForm->getUser()->setScenario("reset");
            $changePasswordForm->getUser()->save(false);
            Yii::$app->session->setFlash("change-password-success", Yii::t("user/default", "Profile updated"));
            return $this->refresh();
        }

        // validate for ajax request
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user, $changePasswordForm);
        }
        // validate for ajax request
        //if ($changePasswordForm->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
        //    Yii::$app->response->format = Response::FORMAT_JSON;
        //    return ActiveForm::validate($changePasswordForm);
        //}

        // render
        return $this->render("profile", [
            'model' => $user,
            'changePasswordForm' => $changePasswordForm
        ]);
    }

    /**
     * Resend email confirmation
     */
    public function actionResend()
    {
        $this->pageName = Yii::t('user/default', 'RESEND');
        // $this->breadcrumbs[] =  $this->pageName;
        $model = Yii::$app->getModule("user")->model("ResendForm");
        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {

            // set flash (which will show on the current page)
            Yii::$app->session->setFlash("resend-success", Yii::t("user/default", "Confirmation email resent"));
        }
        return $this->render("resend", [
            "model" => $model,
        ]);
    }

    /**
     * Resend email change confirmation
     */
    public function actionResendChange()
    {
        $user = Yii::$app->user->identity;
        $userKey = new UserKey;
        $userKey = $userKey::findActiveByUser($user->id, UserKey::TYPE_EMAIL_CHANGE);
        if ($userKey) {

            // send email and set flash message
            $user->sendEmailConfirmation($userKey);
            Yii::$app->session->setFlash("resend-success", Yii::t("user/default", "Confirmation email resent"));
        }

        return $this->redirect(["/user/account"]);
    }

    /**
     * Cancel email change
     */
    public function actionCancel()
    {
        $user = Yii::$app->user->identity;
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::findActiveByUser($user->id, $userKey::TYPE_EMAIL_CHANGE);
        if ($userKey) {

            // remove `user.new_email`
            $user->new_email = null;
            $user->save(false);

            // expire userKey and set flash message
            $userKey->expire();
            Yii::$app->session->setFlash("Cancel-success", Yii::t("user/default", "Email change cancelled"));
        }

        return $this->redirect(["/user/account"]);
    }

    /**
     * Forgot password
     * @return string
     */
    public function actionForgot()
    {
        $config = Yii::$app->settings->get('user');
        // if ($config->enable_forgot) {
        $model = new ForgotForm();
        $this->pageName = Yii::t('user/default', 'FORGOT');
        if ($model->load(Yii::$app->request->post()) && $model->sendForgotEmail()) {

            // set flash (which will show on the current page)
            Yii::$app->session->setFlash("forgot-success", Yii::t("user/default", "FORGOT_SEND_SUCCESS"));
        }

        return $this->render("forgot", [
            "model" => $model,
        ]);
        // } else {
        //     return $this->redirect(['/']);
        // }
    }

    /**
     * Reset password
     *
     * @param $key
     * @return string
     */
    public function actionReset($key)
    {


        $this->pageName = Yii::t('user/default', 'RESET_PASSWORD');
        $this->breadcrumbs[] = $this->pageName;


        $userKey = UserKey::findActiveByKey($key, UserKey::TYPE_PASSWORD_RESET);
        if (!$userKey) {
            return $this->render('reset', ["invalidKey" => true]);
        }

        // get user and set "reset" scenario
        $success = false;
        $user = new User;
        $user = $user::findOne($userKey->user_id);
        $user->setScenario("reset");

        // load post data and reset user password
        if ($user->load(Yii::$app->request->post()) && $user->save()) {

            // consume userKey and set success = true
            $userKey->consume();
            $success = true;
        }

        return $this->render('reset', compact("user", "success"));
    }

}
