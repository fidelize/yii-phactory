<?php

class FPhactoryLoader extends \Phactory\Loader
{
    /**
     * @inheritdoc
     */
    public function load($name)
    {
        $factoryClass = ucfirst($name) . 'Phactory';

        Yii::import('application.tests.factories.' . $factoryClass);

        if (!class_exists($factoryClass)) {
            throw new \Exception("Unknown factory '$name'");
        }

        return new \Phactory\Factory($name, new $factoryClass);
    }
}
