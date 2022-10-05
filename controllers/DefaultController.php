<?php

namespace panix\mod\user\controllers;

use panix\engine\CMS;
use panix\engine\controllers\WebController;
use panix\mod\user\models\forms\ChangePasswordForm;
use panix\mod\user\models\forms\ForgotForm;
use panix\mod\user\models\forms\LoginForm;
use panix\mod\user\models\forms\ResendForm;
use panix\mod\user\models\User;
use panix\mod\user\models\UserKey;
use Yii;
use yii\base\Exception;
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
    public function beforeAction22($action) {
        $formTokenName = '_csrf_cms';

        if ($formTokenValue = Yii::$app->request->post($formTokenName)) {
            $sessionTokenValue = Yii::$app->session->get($formTokenName);

            if ($formTokenValue != $sessionTokenValue ) {
                throw new \yii\web\HttpException(400, 'The form token could not be verified.');
            }

            Yii::$app->session->remove($formTokenName);
        }

        return parent::beforeAction($action);
    }
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
            return $this->redirect(["/user/profile"]);
        }
    }
    public function actionSign()
    {
        $config = Yii::$app->settings->get('user');
        if (Yii::$app->user->isGuest) {
            $this->pageName = Yii::t('user/default', 'LOGIN');
            $this->view->params['breadcrumbs'] = [
                $this->pageName
            ];

            //Login
            $loginModel = new LoginForm();
            $post = Yii::$app->request->post();
            if ($loginModel->load($post)) {
                if (Yii::$app->request->isAjax) {
                    $validator = ActiveForm::validate($loginModel);
                    if ($validator)
                        return $this->asJson($validator);
                }
                if ($loginModel->validate()) {


                    if ($loginModel->login($config->login_duration * 86400)) {
                        if (Yii::$app->request->isAjax) {
                            return $this->asJson([
                                'redirect' => '/',
                                'success' => true
                            ]);
                        } else {
                            Yii::$app->session->setFlash('success-login', 'isLogin');
                            if (isset($post['LoginForm']['returnUrl'])) {
                                return $this->goBack($post['LoginForm']['returnUrl']);
                            } else {
                                return $this->goBack(Yii::$app->getModule("user")->loginRedirect);
                            }
                        }
                    }
                } else {
                    if (isset($loginModel->errors['password'])) {
                        Yii::$app->session->addFlash('error', $loginModel->errors['password'][0]);
                    }
                }
            }


            //register
            $registerModel = new User();
            $registerModel->setScenario('register');


            $registerModel->role = 'user';
            if ($registerModel->load($post)) {

                $registerModel->username = $registerModel->email;
                // validate for ajax request
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($registerModel);
                }

                //print_r($user->attributes);die;
                // validate for normal request
                if ($registerModel->validate()) {

                    try {
                        // perform registration
                        $registerModel->setRegisterAttributes(Yii::$app->request->userIP)->save(false);
                        $this->afterRegister($registerModel);

                        // set flash
                        // don't use $this->refresh() because user may automatically be logged in and get 403 forbidden
                        $successText = Yii::t("user/default", "REGISTER_SUCCESS", ["username" => $registerModel->getDisplayName()]);
                        Yii::$app->session->setFlash("success", $successText);
                        return $this->redirect(Yii::$app->user->loginUrl);

                    } catch (Exception $exception) {

                    }

                }
            }


            // render
            return $this->render('sign', [
                'loginModel' => $loginModel,
                'registerModel' => $registerModel
            ]);

        } else {
            return $this->redirect(['/']);
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
            $this->pageName = Yii::t('user/default', 'LOGIN');
            $this->view->params['breadcrumbs'] = [
                $this->pageName
            ];

            // load post data and login
            $model = new LoginForm();
            $post = Yii::$app->request->post();
            if ($model->load($post)) {

                if (Yii::$app->request->isAjax) {
                    $validator = ActiveForm::validate($model);
                    if ($validator)
                        return $this->asJson($validator);
                }
                if ($model->validate()) {


                    if ($model->login($config->login_duration * 86400)) {
                        if (Yii::$app->request->isAjax) {
                            return $this->asJson([
                                'redirect'=>Yii::$app->getModule("user")->loginRedirect,
                                'success'=>true
                            ]);
                        }else{
                            Yii::$app->session->setFlash('success-login', 'isLogin');
                            if (isset($post['LoginForm']['returnUrl'])) {
                                return $this->goBack($post['LoginForm']['returnUrl']);
                            } else {
                                return $this->goBack(Yii::$app->getModule("user")->loginRedirect);
                            }
                        }
                    }
                } else {
                    if (isset($model->errors['password'])) {
                        Yii::$app->session->addFlash('error', $model->errors['password'][0]);
                    }
                }
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
        if ($config->enable_register && Yii::$app->user->isGuest) {
            $user = new User();
            $user->setScenario('register');
            $this->pageName = Yii::t('user/default', 'REGISTER');
            $this->view->params['breadcrumbs'][] = $this->pageName;
            // load post data
            $post = Yii::$app->request->post();

            $user->role = 'user';
            if ($user->load($post)) {

                $user->username = $user->email;
                // validate for ajax request
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($user);
                }

                //print_r($user->attributes);die;
                // validate for normal request
                if ($user->validate()) {

                    try{
                        // perform registration
                        $user->setRegisterAttributes(Yii::$app->request->userIP)->save(false);
                        $this->afterRegister($user);

                        // set flash
                        // don't use $this->refresh() because user may automatically be logged in and get 403 forbidden
                        $successText = Yii::t("user/default", "REGISTER_SUCCESS", ["username" => $user->getDisplayName()]);
                        Yii::$app->session->setFlash("success", $successText);
						return $this->redirect(Yii::$app->user->loginUrl);

                    }catch (Exception $exception){

                    }

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
        $userKey = new UserKey;
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
        $userKey = new UserKey;

        $userKey = $userKey::findActiveByKey($key, [$userKey::TYPE_EMAIL_ACTIVATE, $userKey::TYPE_EMAIL_CHANGE]);

        if ($userKey) {

            // confirm user
            $user = new User;
            $user = $user::findOne($userKey->user_id);
            $user->confirm();

            // consume userKey and set success
            $userKey->consume();
            $success = $user->email;
        }

        $this->pageName = Yii::t('user/default', $success ? 'CONFIRMED' : 'ERROR');
        $this->view->params['breadcrumbs'][] = $this->pageName;

        // render
        return $this->render("confirm", [
            "userKey" => $userKey,
            "success" => $success
        ]);
    }

    /**
     * Account
     */
    public function actionAccount2()
    {
        //$user = Yii::$app->user->identity;
        $user = Yii::$app->getModule("user")->model("User");
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
                    Yii::$app->session->setFlash("error", "Failed to send email");
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

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        $loadedPost = $model->load(Yii::$app->request->post());
        $this->pageName = Yii::t('user/default', 'CHANGE_PASSWORD');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('user/default', 'PROFILE'),
            'url' => ['profile']
        ];
        $this->view->params['breadcrumbs'][] = $this->pageName;
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($changePasswordForm);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->getUser()->setScenario("reset");
            $model->getUser()->save(false);
	    Yii::$app->session->setFlash("success-change-password", Yii::t("user/default", "UPDATE_SUCCESS_PASSWORD"));
            return $this->refresh();
        }

        return $this->render("change-password", [
            'model' => $model
        ]);
    }

	
    public function actionViewprofile($id)
    {
        $model = User::findOne($id);
        if (!$model)
            $this->error404();

        return $this->render("view-profile", [
            'model' => $model
        ]);

    }
	
    /**
     * Profile
     */
    public function actionProfile()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if (!$user)
            $this->error404();

        //$user->setScenario('profile');
        $this->pageName = Yii::t('user/default', 'PROFILE');
        $this->view->title = $this->pageName;
        $this->view->params['breadcrumbs'][] = $this->pageName;

        //$user = Yii::$app->getModule("user")->model("User");

        $loadedPost = $user->load(Yii::$app->request->post());

        $changePasswordForm = new ChangePasswordForm();

        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->validate()) {
            $changePasswordForm->getUser()->setScenario("reset");
            $changePasswordForm->getUser()->save(false);
            Yii::$app->session->setFlash("success-change-password", Yii::t("user/default", "UPDATE_SUCCESS_PASSWORD"));
            return $this->refresh();
        }
        if ($changePasswordForm->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($changePasswordForm);
        }
        // validate for ajax request
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user); //, $changePasswordForm
        }

        // validate for normal request
        if ($loadedPost && $user->validate()) {

            // generate userKey and send email if user changed his email
            // change email
            if ($user->status == User::STATUS_ACTIVE && Yii::$app->getModule("user")->emailChangeConfirmation && $user->checkAndPrepEmailChange()) {

                $userKey = Yii::$app->getModule("user")->model("UserKey");
                $userKey = $userKey::generate($user->id, $userKey::TYPE_EMAIL_CHANGE);
                if (!$numSent = $user->sendEmailConfirmation($userKey)) {
                    // handle email error
                    Yii::$app->session->setFlash("error", "Failed to send email");
                }
                //Yii::$app->session->addFlash("warning", Yii::t("user/default", "SEND_EMAIL_CONFIRM", $user->getOldAttribute('email')));
            }


            $user->save(false);
            Yii::$app->session->setFlash("success", Yii::t("user/default", "UPDATE_SUCCESS_PROFILE"));
            return $this->refresh();
        }


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
        $this->view->params['breadcrumbs'][] = $this->pageName;
        /** @var ResendForm $model */
        $model = Yii::$app->getModule("user")->model("ResendForm");

        $data = (Yii::$app->user->isGuest) ? Yii::$app->request->post() : ['ResendForm' => Yii::$app->request->get()];
        if ($model->load($data) && $model->sendEmail()) {
            Yii::$app->session->setFlash("success", Yii::t("user/default", "CONFIRM_EMAIL_RESENT"));
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
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $userKey = new UserKey;
        $userKey = $userKey::findActiveByUser($user->id, UserKey::TYPE_EMAIL_CHANGE);
        if ($userKey) {

            // send email and set flash message
            $user->sendEmailConfirmation($userKey);
            Yii::$app->session->setFlash("success", Yii::t("user/default", "RESEND_EMAIL_CONFIRM", $user->new_email));
        }

        return $this->redirect(["/user/default/profile"]);
    }

    /**
     * Cancel email change
     */
    public function actionCancel()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $userKey = new UserKey;
        $userKey = $userKey::findActiveByUser($user->id, $userKey::TYPE_EMAIL_CHANGE);
        if ($userKey) {

            $user->new_email = null;
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);

            // expire userKey and set flash message
            $userKey->expire();
            Yii::$app->session->setFlash("success", Yii::t("user/default", "CHANGE_EMAIL_CANCELED"));
        }

        return $this->redirect(["/user/default/profile"]);
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


        $this->view->title = $this->pageName;
        $this->view->params['breadcrumbs'][] = $this->pageName;

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
        $this->view->params['breadcrumbs'][] = $this->pageName;

        /** @var UserKey $userKey */
        $userKey = UserKey::findActiveByKey($key, UserKey::TYPE_PASSWORD_RESET);
        if (!$userKey) {
            return $this->render('reset', ["invalidKey" => true]);
        }

        // get user and set "reset" scenario
        $success = false;
        $user = User::findOne($userKey->user_id);
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
