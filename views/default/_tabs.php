<?php
use yii\helpers\Url;

$tabs = [];

    $tabs[] = [
        'label' => 'profile',
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
    'content' => '111',
    'url' => Url::to(['/cart/orders']),
    'options' => ['id' => 'v1ideo'],
];



echo \panix\engine\bootstrap\Tabs::widget(['items' => $tabs, 'navType' => 'nav-pills justify-content-center']);


$this->registerJs("
/*$(document).on('click', '.nav a.nav-link', function(){
    var self = $(this);
    $.get(self.attr('href'),{
            what: self.attr('href')
        },
        function(data){
            $('#v1ideo').html(data);
        }
    );
})*/
");
?>