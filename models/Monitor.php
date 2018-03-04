<?php

namespace app\models;

use app\models\Helpers\EAV;
use mirocow\eav\models\EavAttribute;
use mirocow\eav\models\EavEntity;
use mirocow\eav\models\EavEntitySearch;

class Monitor
{
    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255], // Product field
            [['name'], 'required'],
            //[['brand'], 'string', 'max' => 255], // Attribute(s) field
        ];
    }

    public function save()
    {
        $entity = array(
            'entityName' => $this->name,
            'entityModel' => Monitor::class,
        );
        $attributes = array();
        foreach ($this as $key => $value) {
            $attributes[$key] =$value;
        }
        $eav = new EAV($entity, $attributes);
    }


    public function getAllMonitors()
    {
        $entSearch = new EavEntitySearch();
        return $entSearch->search(['entityModel' => Monitor::class]);
    }

}