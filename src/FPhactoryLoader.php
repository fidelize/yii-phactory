<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

class FPhactoryLoader extends \Phactory\Loader
{
    /**
     * @inheritdoc
     */
    public function load(mixed $name): \Phactory\Factory
    {
        if (!is_string($name) || $name === '') {
            throw new \Exception("Unknown factory '$name'");
        }

        $factoryClass = ucfirst($name) . 'Phactory';

        \Yii::import('application.tests.factories.' . $factoryClass);

        if (!class_exists($factoryClass)) {
            throw new \Exception("Unknown factory '$name'");
        }

        return new \Phactory\Factory($name, new $factoryClass());
    }
}
