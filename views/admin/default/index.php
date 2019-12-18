<?php

use yii\helpers\Html;
use panix\engine\grid\GridView;
use panix\engine\CMS;
use panix\mod\user\models\Role;
use panix\mod\user\models\User;
use panix\engine\widgets\Pjax;

$user = new User;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var panix\mod\user\models\search\UserSearch $searchModel
 * @var panix\mod\user\models\User $user
 */
$this->title = Yii::t('user/default', 'Users');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-index">
    <?php Pjax::begin(['dataProvider' => $dataProvider]); ?>
    <?=
    // yii\grid\GridView
    GridView::widget([
        'tableOptions' => ['class' => 'table table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layoutOptions' => [
            'title' => $this->context->pageName
        ],

        'columns' => [
            [
                // 'attribute' => 'role_id',
                'label' => Yii::t('user/default', 'ONLINE'),
                'format' => 'html',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model, $index, $dataColumn) {

                    if (isset($model->session)) {
                        $content = Yii::t('user/default', 'ONLINE');
                        $options = ['class' => 'badge badge-success', 'title' => CMS::time_passed(strtotime($model->session->created_at))];
                    } else {
                        $content = Yii::t('user/default', 'OFFLINE');
                        $options = ['class' => 'badge badge-secondary'];
                    }

                    return Html::tag('span', $content, $options);
                }
            ],
            [
                'attribute' => 'status',
                'filter' => $user::statusDropdown(),
                'value' => function ($model, $index, $dataColumn) use ($user) {
                    $statusDropdown = $user::statusDropdown();
                    return $statusDropdown[$model->status];
                }
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model, $index, $dataColumn) {
                    return CMS::date($model->created_at);
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
    <?php Pjax::end(); ?>
</div>
