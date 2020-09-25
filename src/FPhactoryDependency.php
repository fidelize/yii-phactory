<?php
namespace fidelize\YiiPhactory;

class FPhactoryDependency extends \Phactory\Dependency
{
    /**
     * Checa se a propriedade do Yii existe, porque o ActiveRecord usa
     * métodos mágicos para acessar as propriedades
     * @inheritdoc
     */
    protected function has($part, $subject)
    {
        return (
            method_exists($subject, $part)
            || (is_array($subject) && array_key_exists($part, $subject))
            || (is_object($subject) && property_exists($subject, $part))
            // Yii 
            || (method_exists($subject, 'hasAttribute') && $subject->hasAttribute($part))
            || (method_exists($subject, 'getActiveRelation') && $subject->getActiveRelation($part))
        );
    }
}
