<?php

use yii\helpers\Html;
use panix\engine\CMS;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 * @var yii\widgets\ActiveForm $form
 */


$tabs[] = [
    'label' => 'Общие',
    'content' => $this->render('_main', ['model' => $user]),
    'active' => true,
    'options' => ['id' => 'main'],
];

?>

<?php if (!$user->status && !$user->isNewRecord) { ?>
    <div class="alert alert-warning">
        Аккаунет не
        актевирован. <?= Html::a('отправить владельцу письмо с инструкций?', ['send-active', 'id' => $user->id]); ?>
    </div>
<?php } ?>
<div class="row">
    <div class="col-md-8 col-lg-6 col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <div class="card-body">
                <?php
                echo panix\engine\bootstrap\Tabs::widget([
                    'items' => $tabs,
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-6 col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5>Данные: <?= $user->getDisplayName(); ?></h5>
            </div>
            <div class="card-body">
                <?php
                echo Html::img($user->getAvatarUrl('100x100'), ['class' => 'm-3']);
                ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <?php if ($user->api_key) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('api_key'); ?></th>
                                <td style="width: 70%"><code><?= $user->api_key; ?></code></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->ip_create) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('ip_create'); ?></th>
                                <td style="width: 70%"><?= $user->ip_create; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->login_time) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('login_time'); ?></th>
                                <td style="width: 70%"><?= $user->login_time; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->login_ip) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('login_ip'); ?></th>
                                <td style="width: 70%"><?= CMS::ip($user->login_ip); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->created_at) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('created_at'); ?></th>
                                <td style="width: 70%"><?= CMS::date($user->created_at); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->updated_at) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('updated_at'); ?></th>
                                <td style="width: 70%"><?= CMS::date($user->updated_at); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->login_user_agent) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('login_user_agent'); ?></th>
                                <td style="width: 70%"><?= new \panix\engine\components\Browser($user->login_user_agent); ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>
                <?php if ($user->session) { ?>
                    <h5>Сессия</h5>
                    <?= $user->session->user_agent; ?>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
