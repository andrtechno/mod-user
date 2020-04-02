<?php
use yii\helpers\Url;

$tabs = [];

$tabs[] = [
    'label' => Yii::t('user/default', 'PROFILE'),
    'content' => $this->render('_profile', ['model' => $model, 'changePasswordForm' => $changePasswordForm]),
    //   'active' => true,
    'options' => ['id' => 'description'],
];
$tabs[] = [
    'label' => Yii::t('user/default', 'MY_REVIEWS'),
    'content' => $this->render('profile/_reviews', ['model' => $model]),
    //   'active' => true,
    'options' => ['id' => 'description'],
];

/*$tabs[] = [
    'label' => 'Видео',
    'content' => 'dsadsa',
    //'content' => $this->render('tabs/_video', ['model' => $model]),
    'options' => ['id' => 'video'],
];*/

$tabs[] = [
    'label' => Yii::t('cart/default', 'MY_ORDERS'),
    //'content' => '111',
    //'url' => Url::to(['/cart/orders']),
    //'options' => ['id' => 'v1ideo'],
    'linkOptions' => ['data-url' => Url::to(['/cart/orders'])],
    'tabContentOptions' => ['id' => 'tab-orders'],
    'itemOptions' => ['id' => 'tab-content-orders']
];


echo \panix\engine\bootstrap\Tabs::widget(['items' => $tabs, 'navType' => 'nav-pills justify-content-center']);


$this->registerJs("
    $(document).on('click', '.nav a.nav-link', function(e){
        var self = $(this);

        if($(self.attr('href')).is(':empty') && self.data('url')){
            $.get(self.data('url'),{
                    what: self.data('url')
                },
                function(data){
                    $('#tab-content-orders').html(data);
                }
            );
            $(self.attr('href')).show();
            e.preventDefault();
        }
    });
");
?>