<?php
class FPhactoryFormTest extends FPhactoryTestCase
{
    /**
     * Garante que todos os atributos têm pelo menos uma regra de validação
     */
    public function testRules()
    {
        $object = $this->getModelObject();
        $attributes = $object->attributes;

        foreach ($object->rules() as $rule) {

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
    public function testAttributesLabels()
    {
        $object = $this->getModelObject();
        $labels = $object->attributeLabels();
        $attributes = array_keys($object->attributes);
        $attributesWithoutLabels = array();

        foreach ($attributes as $attribute) {
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
     * @return string
     */
    protected function getModelClass()
    {
        $className = get_called_class();
        return substr($className, 0, strlen($className) - strlen('Test'));
    }

    /**
     * Adivinha o nome do modelo pelo nome da classe de teste
     * @return string
     */
    protected function getModelObject()
    {
        $modelClass = $this->getModelClass();
        return new $modelClass;
    }
}
