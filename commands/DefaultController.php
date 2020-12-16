<?php

namespace panix\mod\user\commands;

use Yii;
use yii\helpers\Console;
use yii\web\HttpException;
use panix\engine\console\controllers\ConsoleController;

/**
 * 1
 */
class DefaultController extends ConsoleController
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        // allow console requests only
        if (!Yii::$app->request->isConsoleRequest) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        parent::init();
    }

    /**
     * Index
     */
    public function actionIndex()
    {
        $this->stdout("Start refresh users points." . PHP_EOL, Console::FG_GREEN);
        $aggregate = 86400 * (int)Yii::$app->settings->get('users', 'bonus_expire_days');
        /** @var \panix\mod\user\models\User $class */
        $class = Yii::$app->user->identityClass;
        $users = $class::find()
            ->where(['<=', 'points_expire', time() - $aggregate])
            ->andWhere(['>', 'points', 0])
            ->all();
        foreach ($users as $user) {
            /** @var \panix\mod\user\models\User $user */
            $user->points = 0;
            $user->points_expire = NULL;
            $user->save(false);
            $this->stdout("Refreshed: {$user->id}" . PHP_EOL, Console::FG_GREEN);
        }
        $this->stdout("Finish refresh users points." . PHP_EOL, Console::FG_GREEN);
    }

}