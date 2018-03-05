<?php

namespace app\models;

use app\models\Helpers\EAV;
use mirocow\eav\models\EavAttribute;
use mirocow\eav\models\EavAttributeSearch;
use mirocow\eav\models\EavAttributeValue;
use mirocow\eav\models\EavEntity;
use mirocow\eav\models\EavEntitySearch;

/**
 * Class Monitor
 * @package app\models
 */
class Monitor
{
    private $attributeRules = array();

    /**
     * Monitor constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->attributeRules);
    }

    /**
     * @param EavEntity $entity
     */
    private function initMonitor(EavEntity $entity = null)
    {
        if (empty($entity)) {
            $entity = new EavEntity();
        }
        $attrSearch = new EavAttributeSearch();

        foreach ($entity as $key => $value) {
            $this->$key = $value;
        }
        $attributes = $attrSearch->search(['entityId' => $entity->id])->getModels();
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $value = EavAttributeValue::find()->where([
                    'entityId' => $entity->id,
                    'attributeId' => $attribute->id])->one();
                if (!empty($value)) {
                    $attributeName = $attribute->getAttribute('name');
                    $attributeValue = $value->getAttribute('value');
                    $this->$attributeName = $attributeValue;

                    $this->attributeRules[] = [[$attributeName], 'required'];
                }
            }
        }
    }

    public function createMonitor()
    {
        $this->initMonitor();
    }

    /**
     *
     */
    public function save()
    {
        if (isset($this->entityName) && !empty($this->entityName)) {
            $entity = array(
                'entityName' => $this->entityName,
                'entityModel' => Monitor::class,
            );
            $ignoredAttributes = array('id', 'entityName', 'entityModel', 'categoryId', 'attributeRules');
            $attributes = array();
            foreach ($this as $key => $value) {
                if(!in_array($key, $ignoredAttributes)) {
                    $key = str_replace(' ', '', lcfirst(ucwords($key)));
                    $attributes[$key] = [
                        'value' => $value
                    ];
                }
            }
            $eav = new EAV();
            if ($eav->saveEAV($entity, $attributes)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getAllMonitors()
    {
        $entSearch = new EavEntitySearch();
        $monitors = array();

        $entities = $entSearch->search(['entityModel' => Monitor::class])->getModels();
        foreach ($entities as $entity) {
            $monitor = new Monitor();
            $monitor->initMonitor($entity);
            $monitors[] = $monitor;
        }
        return $monitors;
    }

    /**
     * @return array
     */
    public function getMonitorAttributes()
    {
        $ret = array();
        foreach ($this as $key => $value) {
            $ret[] = $key;
        }
        return $ret;
    }

}