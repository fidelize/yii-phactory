<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory\Tests;

use fidelize\YiiPhactory\FTestLib;
use PHPUnit\Framework\TestCase;

final class FTestLibTest extends TestCase
{
    public function testGeraCpfAndCnpj(): void
    {
        $cpf = FTestLib::geraCPF(false);
        $this->assertSame(11, strlen($cpf));
        $cnpj = FTestLib::geraCNPJ();
        $this->assertSame(14, strlen($cnpj));
    }
}
