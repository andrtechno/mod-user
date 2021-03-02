<?php

namespace panix\mod\user\models;

use Yii;
use yii\db\ActiveQuery;
use panix\engine\traits\query\DefaultQueryTrait;
use panix\engine\traits\query\TranslateQueryTrait;
use panix\mod\shop\models\traits\EavQueryTrait;
use panix\mod\shop\models\Category;
use panix\mod\shop\models\Product;
use panix\mod\shop\models\ProductCategoryRef;

class UserQuery extends ActiveQuery
{

    use DefaultQueryTrait;

    public function init2()
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addOrderBy(["{$tableName}.id" => SORT_DESC]);
        parent::init();
    }


}
