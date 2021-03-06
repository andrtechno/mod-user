<?php

namespace panix\mod\user\models\search;

use panix\engine\CMS;
use Yii;
use yii\base\Model;
use panix\engine\data\ActiveDataProvider;
use panix\mod\user\models\User;

/**
 * UserSearch represents the model behind the search form about `panix\mod\user\models\User`.
 */
class UserSearch extends User {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'status'], 'integer'],
            [['first_name', 'last_name'], 'string'],
            [['email', 'new_email', 'username', 'password', 'auth_key', 'api_key', 'login_ip', 'login_time', 'ip_create', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributes() {
        // add related fields to searchable attributes
        return parent::attributes();
    }
    public static function getSort()
    {
        return new \yii\data\Sort([
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'attributes' => [
                'id',
                'status',
                'first_name',
                'email',
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => 'по дате добавления'
                ],
            ],
        ]);
    }
    /**
     * Search
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params) {

        // get models
        $user = new User;
        $userTable = $user::tableName();


        $query = $user::find();

        // create data provider
        $dataProvider = new ActiveDataProvider([
                    'query' => $query,
            'sort'=>self::getSort()
                ]);

        // enable sorting for the related columns
       /* $addSortAttributes = [];
        foreach ($addSortAttributes as $addSortAttribute) {
            $dataProvider->sort->defaultOrder = [
                'id' => SORT_DESC,
            ];
            $dataProvider->sort->attributes[$addSortAttribute] = [
                'asc' => [$addSortAttribute => SORT_ASC],
                'desc' => [$addSortAttribute => SORT_DESC],
            ];
        }*/

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            "{$userTable}.id" => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'new_email', $this->new_email])
                ->andFilterWhere(['like', 'username', $this->username])
                ->andFilterWhere(['like', 'password', $this->password])
                ->andFilterWhere(['like', 'auth_key', $this->auth_key])
                ->andFilterWhere(['like', 'api_key', $this->api_key])
                ->andFilterWhere(['like', 'login_ip', $this->login_ip])
                ->andFilterWhere(['like', 'ip_create', $this->ip_create])
                ->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
                ->andFilterWhere(['like', 'login_time', $this->login_time])
                ->andFilterWhere(['like', "{$userTable}.created_at", $this->created_at])
                ->andFilterWhere(['like', "{$userTable}.updated_at", $this->updated_at])
                ->andFilterWhere(['like', 'ban_time', $this->ban_time]);

        if ($this->first_name) {
            $query->andFilterWhere(['like', 'first_name', $this->first_name]);
            $query->orFilterWhere(['like', 'last_name', $this->first_name]);
        }


//echo $query->createCommand()->rawSql;die;
        return $dataProvider;
    }

}