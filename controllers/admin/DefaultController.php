<?php

namespace panix\mod\user\controllers\admin;

use Yii;
use panix\mod\user\models\User;
use panix\mod\user\models\search\UserSearch;
use panix\mod\user\models\UserKey;
use panix\mod\user\models\UserAuth;
use panix\engine\controllers\AdminController;
//use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for User model.
 */
class DefaultController extends AdminController {




    /**
     * @inheritdoc
     */
    public function behaviors2() {
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
    public function actionIndex() {
        $this->pageName = Yii::t('user/default', 'MODULE_NAME');

        $this->buttons = [
            [
                'icon'=>'icon-user',
                'label' => Yii::t('user/default', 'CREATE_USER'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs = [

            $this->pageName
        ];
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
    public function actionView($id) {
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
    public function actionCreate22() {
        /** @var \panix\mod\user\models\User $user */
        /** @var \panix\mod\user\models\Profile $profile */
        $user = new User;
        $user->setScenario("admin");
        $profile = Yii::$app->getModule("user")->model("Profile");

        $post = Yii::$app->request->post();
        if ($user->load($post) && $user->validate() && $profile->load($post) && $profile->validate()) {
            $user->save(false);
            $profile->setUser($user->id)->save(false);
            return $this->redirect(['view', 'id' => $user->id]);
        }

        // render
        return $this->render('create', [
                    'user' => $user,
                    'profile' => $profile,
                ]);
    }

    /**
     * Update an existing User model. If update is successful, the browser
     * will be redirected to the 'view' page.
     *
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {

        $user = User::findModel($id);
        $user->setScenario("admin");


        $this->pageName = Yii::t('user/default', 'MODULE_NAME');

        $this->breadcrumbs = [
            ['label' => $this->pageName, 'url' => ['index']],
            Yii::t('app', 'UPDATE')
        ];


        // load post data and validate
        $post = Yii::$app->request->post();
        if ($user->load($post) && $user->validate()) {
            $user->save(false);
            return $this->redirect(['view', 'id' => $user->id]);
        }

        // render
        return $this->render('update', [
                    'user' => $user,
                ]);
    }

    /**
     * Delete an existing User model. If deletion is successful, the browser
     * will be redirected to the 'index' page.
     *
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        // delete profile and userkeys first to handle foreign key constraint
        $user = User::findModel($id);
        UserKey::deleteAll(['user_id' => $user->id]);
        UserAuth::deleteAll(['user_id' => $user->id]);
        $user->delete();

        return $this->redirect(['index']);
    }


}
