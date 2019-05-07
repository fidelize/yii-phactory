<?php
namespace fidelize\YiiPhactory;

class FPhactoryControllerTest extends FPhactoryTestCase
{
    /**
     * Controller class name or path.
     * @var string application.path.to.controller.ClassName
     */
    protected $classPath;

    /**
     * Controller instance
     * @var CController
     */
    protected $controller;

    /**
     * View file
     * @var array
     */
    protected $viewFile;

    /**
     * View params
     * @var array
     */
    protected $viewParams = array();

    /**
     * Redirect path
     * @var array
     */
    protected $redirectPath;

    /**
     * Forward path
     * @var array
     */
    protected $forwardPath;

    /**
     * Captured filter chain
     * @var CFilterChain
     */
    protected $filterChain;

    /**
     * Rendered JSON
     * @var array
     */
    protected $jsonParam;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        // Create mock
        $this->controller = $this->getControllerMock();

        // Recreate user component
        Yii::app()->setComponent(
            'user',
            Yii::createComponent(['class' => get_class(Yii::app()->user)])
        );

        // Set current application controller
        Yii::app()->setController($this->controller);

        // Mock session component
        Yii::app()->setComponent('session', $this->getSessionMock());

        // Sign out current user
        Yii::app()->user->logout();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        // @TODO
        parent::tearDown();
    }

    /**
     * Sign in with the given user object
     * @param ActiveRecord $user
     * @return void
     */
    protected function signInAs($user)
    {
        $userIdentityMock = $this->getMockBuilder('UserIdentity')
            ->setConstructorArgs(array('', ''))
            ->setMethods(array('getId', 'getName'))
            ->getMock()
        ;
        $userIdentityMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($user->id))
        ;
        $userIdentityMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('username'))
        ;
        Yii::app()->user->login($userIdentityMock);
    }

    /**
     * Sign out
     * @return void
     */
    protected function signOut()
    {
        Yii::app()->user->logout();
    }

    /**
     * Verify if the current user has access to a given action
     * @param  string $action action id
     * @return void
     */
    protected function assertControllerAllowAction($action)
    {
        $this->expectAccessTo($action, true);
    }

    /**
     * Verify if the current user has accesss to a list of actions
     * @param array $actions actions ids
     * @return void
     */
    protected function assertControllerAllowActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->expectAccessTo($action, true);
        }
    }

    /**
     * Verify if the current user has NO access to a given action
     * @param string $action action id
     * @return void
     */
    protected function assertControllerDenyAction($action)
    {
        $this->expectAccessTo($action, false);
    }

    /**
     * Verify if the current user has NO access to a list of actions
     * @param array $actions actions ids
     * @return void
     */
    protected function assertControllerDenyActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->expectAccessTo($action, false);
        }
    }

    /**
     * Expect that the controller registers an user flash message.
     * @param string $type flash message type (like error, success, etc.)
     */
    protected function assertUserHasFlashMessage($type)
    {
        $this->assertNotNull(
            Yii::app()->user->getFlash($type),
            'Flash message not found.'
        );
    }

    /**
     * Expect that a controller method renders the given view
     * @param string $view view file name
     */
    protected function assertControllerRendered($view)
    {
        $this->assertEquals(
            $view,
            $this->viewFile,
            "Didn't render the expected '{$view}'' view."
        );
    }

    /**
     * Expect that a controller method renders the view with the following param.
     * @param string $paramName required param name
     */
    protected function assertControllerRenderedWithParam($paramName)
    {
        $this->assertArrayHasKey(
            $paramName,
            $this->viewParams,
            "Didn't render with the expected '{$paramName}' param."
        );
    }

    /**
     * Expect that a controller method renders a JSON with the following array.
     * @param string $array required json array
     */
    protected function assertControllerRenderedJson($array)
    {
        $this->assertEquals($array, $this->jsonParam, 'Rendered a different JSON');
    }

    /**
     * Return the data to be rendered as JSON by the controller.
     * @param Mixed
     */
    protected function getControllerJsonResponse()
    {
        return $this->jsonParam;
    }

    /**
     * Expect that a controller method renders the view with the following params.
     * @param array $paramsNames required params names
     */
    protected function assertControllerRenderedWithParams(array $paramsNames)
    {
        foreach ($paramsNames as $paramName) {
            $this->assertControllerRenderedWithParam($paramName);
        }
    }

    /**
     * Expect that a controller method rendirects to a given path
     * @return mixed $path
     */
    protected function assertControllerRedirectedTo($path)
    {
        $this->assertEquals(
            $path,
            $this->redirectPath,
            "Didn't redirect to the given path."
        );
    }

    /**
     * Expect that a controller method forwards to a given path
     * @return mixed $path
     */
    protected function assertControllerForwardedTo($path)
    {
        $this->assertEquals(
            $path,
            $this->forwardPath,
            "Didn't redirect to the given path."
        );
    }

    /**
     * @return mixed
     */
    protected function getControllerRenderParam($paramName)
    {
        return $this->viewParams[$paramName];
    }

    /**
     * @return CHttpSession
     */
    protected function getSessionMock()
    {
        return new FPhactorySessionMock;
    }

    /**
     * Set HTTP request method name and params
     * @param string $method HTTP verb
     * @param array $bodyParams request params
     * @param array $queryParams query params
     */
    protected function setRequest($method, array $bodyParams = [], array $queryParams = [])
    {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $_GET = array_merge($bodyParams, $queryParams);
        } else {
            $_POST = $bodyParams;
            $_GET = $queryParams;
        }
    }

    /**
     * Set an Ajax HTTP request method name and params
     * @param string $method HTTP verb
     * @param array $bodyParams request params
     * @param array $queryParams query params
     */
    protected function setAjaxRequest($method, array $bodyParams = [], array $queryParams = [])
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->setRequest($method, $bodyParams, $queryParams);
    }

    /**
     * Get next sequence value for a given model class
     * @param string $modelClass class name
     * @return integer
     */
    protected function getNextIdFor($modelClass)
    {
        return Yii::app()->db->getLastInsertID(
            $modelClass::model()->metadata->tableSchema->sequenceName
        );
    }

    /**
     * @return CController
     */
    protected function getControllerMock(array $methods = [])
    {
        $className = $this->getControllerClassName();
        $id = $this->getControllerId();

        $defaultMethods = [
            'filterAccessControl',
            'forward',
            'invalidActionParams',
            'renderPartial',
            'renderJson',
            'redirect',
            'setFilterId',
        ];

        $methods = array_merge($defaultMethods, $methods);
        $mock = $this->getMockBuilder($className)
            ->setConstructorArgs(array($id))
            ->setMethods($methods)
            ->getMock()
        ;

        // Clear params
        $this->viewFile = null;
        $this->viewParams = [];
        $this->redirectPath = null;
        $this->forwardPath = null;

        // Capture params
        $mock->expects($this->any())
            ->method('renderPartial')
            ->with(
                $this->captureArg($this->viewFile),
                $this->captureArg($this->viewParams)
            )
        ;
        $mock->expects($this->any())
            ->method('redirect')
            ->with($this->captureArg($this->redirectPath))
        ;
        $mock->expects($this->any())
            ->method('forward')
            ->with($this->captureArg($this->forwardPath))
        ;
        $mock->expects($this->any())
            ->method('filterAccessControl')
            ->with($this->captureArg($this->filterChain))
        ;
        $mock->expects($this->any())
            ->method('renderJson')
            ->with($this->captureArg($this->jsonParam))
        ;
        $mock->expects($this->any())
            ->method('setFilterId')
            ->with($this->captureArg($id))
        ;
        return $mock;
    }

    /**
     * @return string controller id
     */
    protected function getControllerId()
    {
        $id = preg_replace('/([A-Z])/', '_$1', $this->getControllerClassName());
        $id = preg_replace('/^_/', '', $id);
        return strtolower($id);
    }

    /**
     * Parse and autoload class name based on the class path.
     * @return string controller class name
     */
    protected function getControllerClassName()
    {
        $className = $this->classPath;

        if (false !== strpos($className, '.')) {
            Yii::import($className);
            $className = explode('.', $className);
            $className = array_pop($className);
        }

        return $className;
    }

    /**
     * Capture a method call argument
     * @see http://stackoverflow.com/questions/7822036/phpunit-get-arguments-to-a-mock-method-call
     */
    protected function captureArg(&$arg)
    {
        return $this->callback(function($argToMock) use (&$arg) {
            $arg = $argToMock;
            return true;
        });
    }

    /**
     * Verify if the current user has access to a given action
     * @param  string $action action id
     * @return void
     */
    protected function expectAccessTo($action, $allowed = true)
    {
        $this->assertFalse(
            empty($this->controller->accessRules()),
            'Controller does not have "accessRules".'
        );
        $filterMock = $this->getMockBuilder('CAccessControlFilter')
            ->setMethods(['accessDenied'])
            ->getMock()
        ;
        $filterMock
            ->expects($allowed ? $this->never() : $this->once())
            ->method('accessDenied')
        ;
        $filterMock->setRules($this->controller->accessRules());
        $filterMock->filter(
            new CFilterChain(
                $this->controller,
                $this->controller->createAction($action)
            )
        );
        $this->controller->expects($this->any())->method('invalidActionParams');
        $this->controller->run($action);
    }
}
