<?php

namespace app\models\Helpers;

use mirocow\eav\models\EavAttribute;
use mirocow\eav\models\EavAttributeOption;
use mirocow\eav\models\EavAttributeRule;
use mirocow\eav\models\EavAttributeType;
use mirocow\eav\models\EavAttributeValue;
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
    /**
     * @var array
     */
    private $attributes = array();
    private $attributeRules = array();

    /**
     * A hozzáadáson dolgoztam.
     * már csak az option, rule és a value mentése van hátra az attributumnak.
     * ezután terv szerint: amikor lekérem az entitást, akkor összerakom egy Monitor példányba, hogy ki tudjam listázni
     * és meg tudjam tekinteni.
     * Lástázás: lapozás és szűrés
     * Megtekintésnél kell valami lehetőség hogy új attribútumokat is hozzátudjak adni
     *
     * installálás: szerintem simán létrehozok egy kontrollert és ott nyomok be neki 50 random monitort, bármennyire is szeretnék
     * migrációt..... de azt is megcsinálom azért, könnyebb visszavonni :)
     *
     * sql-mode: none <--- nem pedit ez a STRICT_TABLE_TRANS mittomén....
     *
     */


    /**
     * EAV constructor.
     */
    public function __construct($entity = [], $attributes = [])
    {
        $this->initEAV($entity, $attributes);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->attributeRules);
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
    public function getEavAttribute($attributeId)
    {
        return EavAttribute::findOne($attributeId);
    }

    /**
     * @param $attributes
     * @return bool
     */
    public function setEavAttribute($attributes)
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
                    foreach ($value as $vKey => $vValue) {
                        if (isset($attr->$vKey)) {
                            $attr->$vKey = $vValue;
                        }
                    }
                    if ($attr->save()) {
                        foreach ($value as $attributeSettingsLabel => $attributeSettingsValue) {
                            if (is_array($attributeSettingsValue)) {
                                switch ($attributeSettingsLabel) {
                                    case 'option':
                                        $this->setEavAttributeOption($attributeSettingsValue, $attr->id);
                                        break;
                                    case 'rule':
                                        $this->setEavAttributeRule($attributeSettingsValue, $attr->id);
                                        break;
                                }
                            }
                            continue;
                        }
                        if ($this->setEavAttributeValue($value['value'], $this->entity->id, $attr->id)) {
                            $this->attributes[] = [
                                $attr->id = $value['value']
                            ];
                        }
                    }
                }
            }
            return (!empty($this->attributes)) ? true : false;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getEavAttributeOptions($attributeId)
    {
        return EavAttributeOption::find()->where(['attributeId' => $attributeId])->all();
    }

    /**
     * @param array $attributeOption
     * @param null $attributeId
     */
    public function setEavAttributeOption($attributeOption = array(), $attributeId = null)
    {
        if (!empty($attributeOption) && !empty($attributeId)) {
            $eavOption = EavAttributeOption::find()->where(['attributeId' => $attributeId])->one();
            if (empty($eavOption)) {
                $eavOption = new EavAttributeOption();
            }
            $eavOption->attributeId = $attributeId;
            foreach ($attributeOption as $key => $value) {
                $eavOption->$key = $value;
            }
            $eavOption->save();
        }
    }

    /**
     * @return mixed
     */
    public function getEavAttributeRules($attributeId)
    {
        return EavAttributeRule::find()->where(['attributeId' => $attributeId])->all();
    }

    /**
     * @param array $attributeRule
     * @param null $attributeId
     */
    public function setEavAttributeRule($attributeRule = array(), $attributeId = null)
    {
        if (!empty($attributeRule) && !empty($attributeId)) {
            $eavRule = EavAttributeRule::find()->where(['attributeId' => $attributeId])->one();
            if (empty($eavRule)) {
                $eavRule = new EavAttributeRule();
            }
            $eavRule->attributeId = $attributeId;
            foreach ($attributeRule as $key => $value) {
                $eavRule->$key = $value;
            }
            if ($eavRule->save()) {
                $attrName = $this->getEavAttribute($attributeId)->name;
                foreach ($attributeRule as $key => $value) {
                    if (isset($eavRule->required) && $eavRule->required) $this->attributeRules[] = [[$attrName], 'required'];
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getEavAttributeType($attributeTypeId)
    {
        return EavAttributeType::findOne($attributeTypeId);
    }

    /**
     * @return mixed
     */
    public function getEavAttributeValue($entityId, $attributeId)
    {
        return EavAttributeValue::find()->where([
            'entityId' => $entityId,
            'attributeId' => $attributeId,
        ])->one();
    }

    /**
     * @param string $attributeValue
     * @param null $entityId
     * @param null $attributeId
     * @param null $optionId
     * @return bool
     */
    public function setEavAttributeValue($attributeValue = '', $entityId = null, $attributeId = null, $optionId = null)
    {
        if (!empty($attributeValue) && !empty($entityId) && !empty($attributeId)) {
            $eavValue = EavAttributeValue::find()->where([
                'entityId' => $entityId,
                'attributeId' => $attributeId
            ])->one();
            if (empty($eavValue)) {
                $eavValue = new EavAttributeValue();
                $eavValue->entityId = $entityId;
                $eavValue->attributeId = $attributeId;
            }
            $eavValue->value = $attributeValue;
            if (!empty($optionId)) {
                $eavValue->optionId = $optionId;
            }
            return $eavValue->save();
        }
        return false;
    }


}