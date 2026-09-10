<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

class FPhactoryFormTest extends FPhactoryTestCase
{
    /**
     * Garante que todos os atributos têm pelo menos uma regra de validação
     */
    public function testRules(): void
    {
        $object = $this->getModelObject();
        $attributes = $object->attributes;

        foreach ($object->rules() as $rule) {
            if (!is_array($rule) || !isset($rule[0]) || !is_string($rule[0])) {
                continue;
            }

            $ruleAttributes = explode(',', $rule[0]);

            foreach ($ruleAttributes as $attribute) {
                $attribute = trim($attribute);

                if (array_key_exists($attribute, $attributes)) {
                    unset($attributes[$attribute]);
                }
            }
        }

        if (count($attributes)) {
            $attributes = implode('", "', array_keys($attributes));
            $message = 'Os atributos "' . $attributes . '" não possuem nenhuma regra de validação.';

            $this->fail($message);
        }
    }

    /**
     * Verifica se todos os atributos possuem um label
     */
    public function testAttributesLabels(): void
    {
        $object = $this->getModelObject();
        $labels = $object->attributeLabels();
        $attributes = array_keys($object->attributes);
        $attributesWithoutLabels = [];

        foreach ($attributes as $attribute) {
            if (!is_string($attribute)) {
                continue;
            }

            if (!isset($labels[$attribute])) {
                $attributesWithoutLabels[] = $attribute;
            }
        }

        if (count($attributesWithoutLabels)) {
            $attributes = implode('", "', $attributesWithoutLabels);
            $message = 'Os atributos "' . $attributes . '" não possuem label.';

            $this->fail($message);
        }
    }

    /**
     * Adivinha o nome do modelo pelo nome da classe de teste
     */
    protected function getModelClass(): string
    {
        $className = static::class;

        return substr($className, 0, strlen($className) - strlen('Test'));
    }

    /**
     * Adivinha o nome do modelo pelo nome da classe de teste
     */
    protected function getModelObject(): \CActiveRecord
    {
        $modelClass = $this->getModelClass();

        if (!class_exists($modelClass)) {
            throw new \RuntimeException('Model class does not exist: ' . $modelClass);
        }

        $object = new $modelClass();

        if (!$object instanceof \CActiveRecord) {
            throw new \RuntimeException('Model class must extend CActiveRecord: ' . $modelClass);
        }

        return $object;
    }
}
