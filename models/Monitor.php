<?php

namespace app\models;

use app\models\Helpers\EAV;
use mirocow\eav\models\EavAttribute;
use mirocow\eav\models\EavAttributeOption;
use mirocow\eav\models\EavAttributeRule;
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

        foreach ($entity as $key => $value) {
            $this->$key = $value;
        }
        $attributes = EavAttribute::find()->where(['entityId' => $entity->id])->all();
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

    public function createMonitor($monitorEav = null)
    {
        $this->initMonitor($monitorEav);
    }

    /**
     *
     */
    public function save()
    {
        if (isset($this->entityName) && !empty($this->entityName)) {
            $entityAttributes = array('id', 'entityName', 'entityModel', 'categoryId');
            $entity = array();
            $attributes = array();
            foreach ($this as $key => $value) {
                if (!in_array($key, $entityAttributes)) {
                    if ($key != 'attributeRules') {
                        $key = str_replace(' ', '', lcfirst(ucwords($key)));
                        $attributes[$key] = [
                            'value' => $value
                        ];
                    }
                } else {
                    if ($key != 'attributeRules') {
                        $entity[$key] = $value;
                    }
                    $entity['entityModel'] = Monitor::class;
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
        $monitors = array();
        $entities = EavEntity::find()->where(['entityModel' => Monitor::class])->all();
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

    public function delete()
    {
        if (isset($this->id) && !empty($this->id)) {
            $attrs = EavAttribute::find()->where(["entityId" => $this->id])->all();
            foreach ($attrs as $attr) {
                if ($d = EavAttributeOption::find()->where(['attributeId' => $attr->id])->one()) {
                    $d->delete();
                }
                if ($d = EavAttributeRule::find()->where(['attributeId' => $attr->id])->one()) {
                    $d->delete();
                }
                if ($d = EavAttributeValue::find()->where(['attributeId' => $attr->id])->one()) {
                    $d->delete();
                }
            }
            $ent = EavEntity::findOne($this->id);
            $ent->delete();
        }

    }

}