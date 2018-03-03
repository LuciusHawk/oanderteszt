<?php

namespace app\models;
use mirocow\eav\models\EavEntity;

/**
 * This is the ActiveQuery class for [[Monitor]].
 *
 * @see Monitor
 */
class MonitorQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Monitor[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Monitor|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
