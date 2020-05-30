<?php

namespace panix\mod\user\controllers\admin;

use panix\engine\bootstrap\ActiveForm;
use panix\mod\user\models\forms\ChangePasswordForm;
use panix\mod\user\models\forms\ForgotForm;
use panix\mod\user\models\forms\ResendForm;
use Yii;
use panix\mod\user\models\User;
use panix\mod\user\models\search\UserSearch;
use panix\mod\user\models\UserKey;
use panix\mod\user\models\UserAuth;
use panix\engine\controllers\AdminController;
//use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AdminController implements the CRUD actions for User model.
 */
class DefaultController extends AdminController
{

    public $icon = 'users';

    public function actions()
    {
        return [
            'deleteFile' => [
                'class' => \panix\engine\actions\DeleteFileAction::class,
                'modelClass' => User::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors2()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * List all User models
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->pageName = Yii::t('user/default', 'MODULE_NAME');
        if (Yii::$app->user->can("/{$this->module->id}/{$this->id}/*") ||  Yii::$app->user->can("/{$this->module->id}/{$this->id}/create")) {
            $this->buttons = [
                [
                    'icon' => 'user-outline',
                    'label' => Yii::t('user/default', 'CREATE_USER'),
                    'url' => ['create'],
                    'options' => ['class' => 'btn btn-success']
                ]
            ];
        }
        $this->breadcrumbs = [$this->pageName];
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Display a single User model
     *
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'user' => User::findModel($id),
        ]);
    }

    /**
     * Create a new User model. If creation is successful, the browser will
     * be redirected to the 'view' page.
     *
     * @return mixed
     */

    /**
     * Update an existing User model. If update is successful, the browser
     * will be redirected to the 'view' page.
     *
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $user = User::findModel($id);
        $user->setScenario('admin');


        $this->pageName = Yii::t('user/default', 'MODULE_NAME');

        $this->breadcrumbs = [
            ['label' => $this->pageName, 'url' => ['index']],
            Yii::t('app/default', 'UPDATE')
        ];

        foreach (Yii::$app->authManager->getRolesByUser($user->id) as $role) {
            $user->role[] = $role->name;
        }

        $this->buttons = [
            [
                'label' => Yii::t('user/default', 'Сбросить пароль и отправить на E-mail'),
                'url' => ['reset-password', 'id' => $user->id],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $loadedPost = $user->load(Yii::$app->request->post());


        $changePasswordForm = new ChangePasswordForm();
        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->validate()) {
            $changePasswordForm->getUser()->setScenario("reset");
            $changePasswordForm->getUser()->new_password = $changePasswordForm->new_password;
            $changePasswordForm->getUser()->save(false);
            Yii::$app->session->setFlash("success", Yii::t("user/default", "CHANGE_PASSWORD_SUCCESS"));
            return $this->refresh();
        }


        $isNew = $user->isNewRecord;
        $post = Yii::$app->request->post();
        if ($loadedPost && $user->validate()) {
            $user->save(false);
            return $this->redirectPage($isNew, $post);
        }
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user, $changePasswordForm);
        }
        // render
        return $this->render('update', ['user' => $user, 'changePasswordForm' => $changePasswordForm]);
    }

    /**
     * Delete an existing User model. If deletion is successful, the browser
     * will be redirected to the 'index' page.
     *
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // delete profile and userkeys first to handle foreign key constraint
        $user = User::findModel($id);
        UserKey::deleteAll(['user_id' => $user->id]);
        UserAuth::deleteAll(['user_id' => $user->id]);
        $user->delete();

        return $this->redirect(['index']);
    }

    public function actionResetPassword($id)
    {
        /** @var User $user */
        $user = User::findModel($id);
        $model = new ForgotForm();
        $this->pageName = Yii::t('user/default', 'FORGOT');
        if ($model->load(['ForgotForm' => ['email' => $user->email]]) && $model->sendForgotEmail()) {
            Yii::$app->session->setFlash("success", Yii::t("user/default", "FORGOT_SEND_SUCCESS"));
        }
        return $this->redirect(['update', 'id' => $user->id]);
    }

    public function actionSendActive($id)
    {
        /** @var User $user */
        $user = User::findModel($id);
        /** @var ResendForm $model */
        $model = Yii::$app->getModule("user")->model("ResendForm");

        if ($model->load(['ResendForm' => ['email' => $user->email]]) && $model->sendEmail()) {
            Yii::$app->session->setFlash("success", Yii::t("user/default", "CONFIRM_EMAIL_RESENT"));
        }
        return $this->redirect(['update', 'id' => $user->id]);
    }
}
