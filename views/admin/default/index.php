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


Pjax::begin(['dataProvider' => $dataProvider]);
echo GridView::widget([
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
            'header' => 'Имя Фамилия',
            'attribute' => 'first_name',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model, $index, $dataColumn) {
                return $model->first_name . ' ' . $model->last_name;
            },
        ],
        [
            'attribute' => 'status',
            'filter' => $user::statusDropdown(),
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model, $index, $dataColumn) use ($user) {
                $statusDropdown = $user::statusDropdown();
                if ($model->status == 1) {
                    $options['class'] = 'badge badge-success';
                } elseif ($model->status == 2) {
                    $options['class'] = 'badge badge-secondary';
                } else {
                    $options['class'] = 'badge badge-warning';
                }
                return Html::tag('span', $statusDropdown[$model->status], $options);
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
        /*[
            'attribute' => 'roles',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model, $index, $dataColumn) {
                return $model->role;
            },
        ],*/
        [
            'header' => 'Бонусов',
            'attribute' => 'points',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model, $index, $dataColumn) {
                return $model->points;
            },
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update}{auth}{delete}',
            'buttons' => [
                'auth' => function ($url, $model, $key) {
                    if ($model->api_key && Yii::$app->user->id == 1 && Yii::$app->user->id != $model->id) {
                        return Html::a('<i class="icon-key"></i>', ['login-api', 'token' => $model->api_key], [
                            'title' => Yii::t('app/default', 'Войти под этим пользователем'),
                            'data-pjax' => 0,
                            'class' => 'btn btn-sm btn-outline-secondary']);
                    }
                }
            ]
        ]
    ],
]);
Pjax::end();


$composer = json_decode(file_get_contents(Yii::getAlias('@user') . DIRECTORY_SEPARATOR . 'composer.json'));

$name = str_replace('https://github.com/', '', $composer->homepage);
$url = "https://raw.githubusercontent.com/{$name}/master/guide/ru/index.md";


$client = new \yii\httpclient\Client(['baseUrl' => $url]);
$response = $client->createRequest()->send();
if ($response->isOk) {
//echo \yii\helpers\Markdown::process($response->content, 'gfm');
}
?>

