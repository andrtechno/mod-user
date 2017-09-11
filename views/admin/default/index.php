<?php

//use yii\helpers\Html;
use panix\engine\grid\AdminGridView;
use panix\engine\CMS;
use yii\helpers\Html;
$user = Yii::$app->getModule("user")->model("User");
$role = Yii::$app->getModule("user")->model("Role");

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var amnah\yii2\user\models\search\UserSearch $searchModel
 * @var amnah\yii2\user\models\User $user
 * @var amnah\yii2\user\models\Role $role
 */
$this->title = Yii::t('user/default', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?php \yii\widgets\Pjax::begin(); ?>
    <?=
   // yii\grid\GridView
    AdminGridView::widget([
        'tableOptions' => ['class' => 'table table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => $this->render('@admin/views/layouts/_grid_layout', ['title' => $this->context->pageName]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
               // 'attribute' => 'role_id',
                'label' => Yii::t('user/default', 'Online'),
                'format'=>'html',
                'contentOptions'=>['class'=>'text-center'],
                'value' => function($model, $index, $dataColumn) {

                    if(isset($model->session)){
                        $content = 'В сети';
                        $options = ['class'=>'label label-success','title'=>date('Y-m-d H:i:s',$model->session->expire)];
                    }else{
                        $content = 'Нет в сети';
                         $options = ['class'=>'label label-default'];
                    }
                    
                    return Html::tag('span', $content, $options);
        
                    return (isset($model->session))?$model->session->expire:'none';
                },
            ],
            'session.expire',
            [
                'attribute' => 'role_id',
                'label' => Yii::t('user/default', 'Role'),
                'filter' => $role::dropdown(),
                'value' => function($model, $index, $dataColumn) use ($role) {
                    $roleDropdown = $role::dropdown();
                    return $roleDropdown[$model->role_id];
                },
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('user/default', 'Status'),
                'filter' => $user::statusDropdown(),
                'value' => function($model, $index, $dataColumn) use ($user) {
                    $statusDropdown = $user::statusDropdown();
                    return $statusDropdown[$model->status];
                },
            ],
            'email:email',
            'profile.full_name',
            [
                'attribute' => 'create_time',
                'label' => Yii::t('user/default', 'create_time'),
                'value' => function($model, $index, $dataColumn) use ($role) {
                    return CMS::date($model->create_time);
                },
            ],
                        
            // 'new_email:email',
            // 'username',
            // 'password',
            // 'auth_key',
            // 'api_key',
            // 'login_ip',
            // 'login_time',
            // 'create_ip',
            // 'create_time',
            // 'update_time',
            // 'ban_time',
            // 'ban_reason',

            ['class' => 'panix\engine\grid\columns\ActionColumn']
            ],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
