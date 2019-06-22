<?php
use yii\helpers\Url;

$tabs = [];

$tabs[] = [
    'label' => Yii::t('user/default', 'PROFILE'),
    'content' => 'adsads',
    //   'active' => true,
    'options' => ['id' => 'description'],
];


$tabs[] = [
    'label' => 'Видео',
    'content' => 'dsadsa',
    //'content' => $this->render('tabs/_video', ['model' => $model]),
    'options' => ['id' => 'video'],
];

$tabs[] = [
    'label' => 'Мои заказы',
    //'content' => '111',
    //'url' => Url::to(['/cart/orders']),
    //'options' => ['id' => 'v1ideo'],
    'linkOptions' => ['data-url' => Url::to(['/cart/orders'])],
    'tabContentOptions' => ['id' => 'vvv'],
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