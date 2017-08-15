mod-user
===========
Module for CORNER CMS

[![Latest Stable Version](https://poser.pugx.org/panix/mod-user/v/stable)](https://packagist.org/packages/panix/mod-user) [![Total Downloads](https://poser.pugx.org/panix/mod-user/downloads)](https://packagist.org/packages/panix/mod-user) [![Monthly Downloads](https://poser.pugx.org/panix/mod-user/d/monthly)](https://packagist.org/packages/panix/mod-user) [![Daily Downloads](https://poser.pugx.org/panix/mod-user/d/daily)](https://packagist.org/packages/panix/mod-user) [![Latest Unstable Version](https://poser.pugx.org/panix/mod-user/v/unstable)](https://packagist.org/packages/panix/mod-user) [![License](https://poser.pugx.org/panix/mod-user/license)](https://packagist.org/packages/panix/mod-user)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist panix/mod-user "*"
```

or add

```
"panix/mod-user": "*"
```

to the require section of your `composer.json` file.

Add to web config.
```
'modules' => [
    'user' => ['class' => 'panix\user\Module'],
],


and

'components' => [
    'user' => [
        'class' => 'panix\user\components\User',
        // 'identityClass' => 'panix\user\models\User',
        // 'enableAutoLogin' => false,
    ],
    ...
]
```