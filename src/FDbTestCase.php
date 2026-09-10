<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

use PHPUnit\Framework\TestCase;

class FDbTestCase extends TestCase
{
    /**
     * @var array<string, string|false>
     */
    protected array $fixtures = [];

    protected function setUp(): void
    {
        if (str_contains($this->name(), 'testWithoutDb')) {
            return;
        }
        parent::setUp();
    }
}
