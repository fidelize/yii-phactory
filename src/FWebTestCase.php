<?php

Yii::import('system.test.CWebTestCase'); 

class FWebTestCase extends CWebTestCase
{
    /**
     * @var array ativa o uso de fixtures por padrão
     */
    protected $fixtures = array();

	/**
     * Sets up before each test method runs.
     * This mainly sets the base URL for the test application.
     */
    protected function setUp()
    {
        if (!defined('TEST_BASE_URL')) {
            throw new Exception('Configure a define TEST_BASE_URL apontando para a URL do index-test.php');
        }

        if (null == $this->coverageScriptUrl) {

            $url = explode('/', TEST_BASE_URL);

            array_pop($url);
            array_pop($url);

            $url[] = 'coverage.php';

            $this->coverageScriptUrl = implode('/', $url);
        }

        parent::setUp();

        $this->setBrowserUrl(TEST_BASE_URL);
    }

    /**
     * @param string $url
     * @param string $username
     * @param string $password, por padrão igual ao $username
     */
    public function openAfterLogin($url, $username, $password = null)
    {
        if (!$password) {
            $password = $username;
        }
        
        $this->open('?r=site/logout');
        $this->open('?r=site/login');
        $this->type('id=LoginForm_username', $username);
        $this->type('id=LoginForm_password', $password);
        $this->clickAndWait("//input[@type='submit']");

        $this->open($url);
    }

    /**
     * Assegura um status 403 (não suportado pelo PHPUnit)
     */
    public function assertUnauthorized()
    {
        $mensagem = 'Você não está autorizado a realizar essa operação.';

        if (preg_replace('/[^a-z0-9]/', '', strtolower(Yii::app()->charset)) == 'utf8') {
            $mensagem = utf8_encode($mensagem);
        }

        return $this->assertTextPresent($mensagem);
    }
}