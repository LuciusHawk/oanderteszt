<?php

namespace app\models\Helpers;

use mirocow\eav\models\EavAttribute;
use mirocow\eav\models\EavEntity;
use yii\db\ActiveRecord;

/**
 * Class EAV
 * @package app\models\Helpers
 */
class EAV extends ActiveRecord
{
    /**
     * @var
     */
    private $entity;
    private $attribute;
    private $attributeOption;
    private $attributeRule;
    private $attributeType;
    private $attributeValue;

    /**
     * EAV constructor.
     */
    public function __construct($entity = [], $attributes = [])
    {
        $this->initEAV($entity, $attributes);
    }

    private function initEAV($entity = [], $attributes = [])
    {
        if ($this->setEntity($entity)) {
            if ($this->setEavAttribute($attributes)) ;
        }
    }


    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $entity
     * @return bool|EavEntity|null|string|static
     */
    public function setEntity($entity)
    {
        $ent = '';
        if (isset($entity['id']) && !empty($entity['id'])) {
            $ent = EavEntity::findOne($entity['id']);
        } elseif (!empty($entity)) {
            $ent = new EavEntity();
        }
        if (!empty($ent)) {
            foreach ($entity as $key => $value) {
                $ent->$key = $value;
            }
            if ($ent->save()) {
                return $this->entity = $ent;
            }
        }
        return $this->entity = false;
    }

    /**
     * @return mixed
     */
    public function getEavAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function setEavAttribute($attribute)
    {
        if ($this->entity && !empty($attributes)) {
            foreach ($attributes as $key => $value) {
                if (is_array($value)) {
                    $attr = EavAttribute::find()->where(['name' => $key])->one();
                    if (empty($attr)) {
                        $attr = new EavAttribute();
                    }
                    $attr->entityId = $this->entity->id;
                    $attr->name = $key;

                    foreach ($value as $valueKey => $v) {
                        if (isset($attr->$valueKey)) {
                            $attr->$valueKey = $v;
                        }
                    }
                    if ($attr->save()) {
                        $this->attribute = $attr->id;

                        foreach ($value as $attributeSettingsLabel => $attributeSettingsValue) {
                            if (is_array($attributeSettingsValue)) {
                                switch ($attributeSettingsLabel) {
                                    case 'option':
                                        $this->setEavAttributeOption($attributeSettingsValue);
                                        break;
                                    case 'rule':
                                        $this->setEavAttributeRule($attributeSettingsValue);
                                        break;
                                }
                            }
                            continue;
                        }
                        $this->setEavAttributeValue($value['value']);
                    }
                }
            }

        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getEavAttributeOption()
    {
        return $this->attributeOption;
    }

    /**
     * @param mixed $attributeOption
     */
    public function setEavAttributeOption($attributeOption)
    {
        $this->attributeOption = $attributeOption;
    }

    /**
     * @return mixed
     */
    public function getEavAttributeRule()
    {
        return $this->attributeRule;
    }

    /**
     * @param mixed $attributeRule
     */
    public function setEavAttributeRule($attributeRule)
    {
        $this->attributeRule = $attributeRule;
    }

    /**
     * @return mixed
     */
    public function getEavAttributeType()
    {
        return $this->attributeType;
    }

    /**
     * @param mixed $attributeType
     */
    public function setEavAttributeType($attributeType)
    {
        $this->attributeType = $attributeType;
    }

    /**
     * @return mixed
     */
    public function getEavAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * @param mixed $attributeValue
     */
    public function setEavAttributeValue($attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }


}