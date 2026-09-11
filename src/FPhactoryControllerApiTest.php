<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

class FPhactoryControllerApiTest extends FPhactoryControllerTest
{
    /**
     * Expect that a controller method renders the given view
     */
    protected function assertApiReturns(string $view): void
    {
        $this->assertEquals(
            $view,
            $this->viewFile,
            "Didn't match the expected return.",
        );
    }
}
