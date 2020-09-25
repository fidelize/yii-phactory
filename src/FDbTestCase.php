<?php

namespace fidelize\YiiPhactory;
use PHPUnit\Framework\TestCase;

class FDbTestCase extends TestCase
{
	/**
	 * Ativa por padrão a limpeza do banco de dados após a execução de cada teste.
	 *
	 * @var array a list of fixtures that should be loaded before each test method executes.
	 * The array keys are fixture names, and the array values are either AR class names
	 * or table names. If table names, they must begin with a colon character (e.g. 'Post'
	 * means an AR class, while ':post' means a table name).
	 * Defaults to false, meaning fixtures will not be used at all.
	 */
	protected $fixtures = array();

    protected function setUp()
    {
        if (strpos($this->getName(), 'testWithoutDb') !== false) {
            return;
        }
        parent::setUp();
    }
}