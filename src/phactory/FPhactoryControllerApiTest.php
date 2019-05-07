<?php

Yii::import('fidelize.tests.phactory.FPhactoryTestCase');

class FPhactoryControllerApiTest extends FPhactoryControllerTest
{
    /**
     * Expect that a controller method renders the given view
     * @param string $view view file name
     */
    protected function assertApiReturns($view)
    {
        $this->assertEquals(
            $view,
            $this->viewFile,
            "Didn't match the expected return."
        );
    }
}
