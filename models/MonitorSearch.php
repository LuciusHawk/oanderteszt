<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class MonitorSearch extends Monitor
{
    // add the public attributes that will be used to store the data to be search
    public $name;
    public $resolution;

    // now set the rules to make those attributes safe
    public function rules()
    {
        return [
            // ... more stuff here
            [['name', 'resolution'], 'safe'],
            // ... more stuff here
        ];
    }

    public function search($params)
    {
        // create ActiveQuery
        $query = Monitor::find();
        // Important: lets join the query with our previously mentioned relations
        // I do not make any other configuration like aliases or whatever, feel free
        // to investigate that your self

       // $query->andEavFilterWhere('=', 'name', \Yii::$app->getRequest()->get('name'));
       // $query->andEavFilterWhere('=', 'resolution', \Yii::$app->getRequest()->get('resolution'));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // No search? Then return data Provider
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}