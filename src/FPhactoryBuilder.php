<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

class FPhactoryBuilder extends \Phactory\Builder
{
    /**
     * @param array<string, mixed> $values
     */
    protected function toObject(mixed $phactoryName, mixed $values): mixed
    {
        $name = is_string($phactoryName) ? $phactoryName : '';
        $valueMap = is_array($values) ? $values : [];
        $className = ucfirst($name);
        $object = new $className();

        foreach ($valueMap as $key => $value) {
            $object->$key = $value;

            if ($object instanceof \CActiveRecord && is_string($key)) {
                $this->setRelation($object, $key, $valueMap);
            }
        }

        return $object;
    }

    protected function saveObject(mixed $name, mixed $object): mixed
    {
        if ($object instanceof \CActiveRecord) {
            if (false == $object->save()) {
                $message = 'Couldn\'t save the object "'
                         . $object::class . '": '
                         . print_r($object->errors, true);

                throw new \Exception($message);
            }
        }

        return $object;
    }

    /**
     * Prenche a relation de um objeto
     *
     * @param array<string, mixed> $values
     */
    protected function setRelation(\CActiveRecord $object, string $attribute, array $values): void
    {
        $relation = $object->getActiveRelation($attribute);

        // Se é uma relation
        if ($relation) {
            $relationAttribute = $relation->foreignKey;
            if (is_array($relationAttribute)) {
                foreach ($relationAttribute as $pk) {
                    if (is_string($pk)) {
                        $this->setRelationAttribute($object, $attribute, $values, $pk);
                    }
                }

                return;
            }
            if (is_string($relationAttribute)) {
                $this->setRelationAttribute($object, $attribute, $values, $relationAttribute);
            }
        }
    }

    /**
     * @param array<string, mixed> $values
     */
    protected function setRelationAttribute(\CActiveRecord $object, string $attribute, array $values, string $relationAttribute): void
    {
        // Se não foi setado o campo relation_id
        if (empty($values[$relationAttribute])) {
            // Se o valor é um objeto, atribui o id dele a relation_id
            $related = $object->$attribute;
            if (is_object($related) && isset($related->id)) {
                $object->$relationAttribute = $related->id;
            } elseif (null === $related) {
                $object->$relationAttribute = null;
            }
        }
    }
}
