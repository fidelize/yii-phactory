<?php
namespace fidelize\YiiPhactory;

class FPhactoryBuilder extends Phactory\Builder
{
    protected function toObject($phactoryName, $values)
    {
        $className = ucfirst($phactoryName);
        $object = new $className;

        foreach ($values as $key => $value) {

            $object->$key = $value;

            if ($object instanceof CActiveRecord) {
                $this->setRelation($object, $key, $values);
            }
        }

        return $object;
    }

    protected function saveObject($name, $object)
    {
        if ($object instanceof CActiveRecord) {

            if (false == $object->save()) {

                $message = 'Couldn\'t save the object "'
                         . get_class($object) . '": '
                         . print_r($object->errors, true)
                ;

                throw new \Exception($message);
            }
        }

        return $object;
    }

    /**
     * Prenche a relation de um objeto
     * @param CActiveRecord $object
     * @param string $attribute
     * @param array $values
     */
    protected function setRelation($object, $attribute, $values)
    {
        $relation = $object->getActiveRelation($attribute);

        // Se é uma relation
        if ($relation) {

            $relationAttribute = $relation->foreignKey;
            if (is_array($relationAttribute)) {
                foreach ($relationAttribute as $fk => $pk) {
                    $this->setRelationAttribute($object, $attribute, $values, $pk);
                }
                return;
            }
            $this->setRelationAttribute($object, $attribute, $values, $relationAttribute);
        }
    }

    protected function setRelationAttribute($object, $attribute, $values, $relationAttribute)
    {
        // Se não foi setado o campo relation_id
        if (empty($values[$relationAttribute])) {
            // Se o valor é um objeto, atribui o id dele a relation_id
            if (is_object($object->$attribute)) {
                $object->$relationAttribute = $object->$attribute->id;
            } elseif (null === $object->$attribute) {
                $object->$relationAttribute = null;
            }
        }
    }
}
