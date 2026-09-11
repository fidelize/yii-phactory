<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

class FPhactoryDependency extends \Phactory\Dependency
{
    /**
     * Checa se a propriedade do Yii existe, porque o ActiveRecord usa
     * métodos mágicos para acessar as propriedades
     *
     * @param mixed $part
     * @param mixed $subject
     */
    protected function has(mixed $part, mixed $subject): bool
    {
        if (!is_string($part)) {
            return false;
        }

        if (is_array($subject)) {
            return array_key_exists($part, $subject);
        }

        if (!is_object($subject)) {
            return false;
        }

        return method_exists($subject, $part)
            || property_exists($subject, $part)
            || (method_exists($subject, 'hasAttribute') && $subject->hasAttribute($part))
            || (method_exists($subject, 'getActiveRelation') && $subject->getActiveRelation($part) !== null);
    }
}
