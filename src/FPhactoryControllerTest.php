<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;

class FPhactoryControllerTest extends FPhactoryTestCase
{
    /**
     * Controller class name or path.
     *
     * @var string application.path.to.controller.ClassName
     */
    protected string $classPath = '';

    /**
     * Controller instance
     */
    protected MockObject&\CController $controller;

    /**
     * View file
     */
    protected mixed $viewFile = null;

    /**
     * View params
     *
     * @var array<string, mixed>
     */
    protected array $viewParams = [];

    /**
     * Redirect path
     */
    protected mixed $redirectPath = null;

    /**
     * Forward path
     */
    protected mixed $forwardPath = null;

    /**
     * Captured filter chain
     */
    protected mixed $filterChain = null;

    /**
     * Rendered JSON
     */
    protected mixed $jsonParam = null;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create mock
        $this->controller = $this->getControllerMock();

        // Recreate user component
        \Yii::app()->setComponent(
            'user',
            \Yii::createComponent(['class' => \Yii::app()->user::class]),
        );

        // Set current application controller
        \Yii::app()->setController($this->controller);

        // Mock session component
        \Yii::app()->setComponent('session', $this->getSessionMock());

        // Sign out current user
        \Yii::app()->user->logout();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        // @TODO
        parent::tearDown();
    }

    /**
     * Sign in with the given user object
     */
    protected function signInAs(object $user): void
    {
        $userId = $user instanceof \CActiveRecord ? $user->id : null;

        if ($userId === null && property_exists($user, 'id')) {
            $userId = $user->id;
        }

        $userIdentityMock = $this->getMockBuilder(\UserIdentity::class)
            ->setConstructorArgs(['', ''])
            ->onlyMethods(['getId', 'getName'])
            ->getMock();
        $userIdentityMock->expects($this->any())
            ->method('getId')
            ->willReturn($userId);
        $userIdentityMock->expects($this->any())
            ->method('getName')
            ->willReturn('username');
        \Yii::app()->user->login($userIdentityMock);
    }

    /**
     * Sign out
     */
    protected function signOut(): void
    {
        \Yii::app()->user->logout();
    }

    /**
     * Verify if the current user has access to a given action
     */
    protected function assertControllerAllowAction(string $action): void
    {
        $this->expectAccessTo($action, true);
    }

    /**
     * Verify if the current user has accesss to a list of actions
     *
     * @param array<int, string> $actions
     */
    protected function assertControllerAllowActions(array $actions): void
    {
        foreach ($actions as $action) {
            $this->expectAccessTo($action, true);
        }
    }

    /**
     * Verify if the current user has NO access to a given action
     */
    protected function assertControllerDenyAction(string $action): void
    {
        $this->expectAccessTo($action, false);
    }

    /**
     * Verify if the current user has NO access to a list of actions
     *
     * @param array<int, string> $actions
     */
    protected function assertControllerDenyActions(array $actions): void
    {
        foreach ($actions as $action) {
            $this->expectAccessTo($action, false);
        }
    }

    /**
     * Expect that the controller registers an user flash message.
     */
    protected function assertUserHasFlashMessage(string $type): void
    {
        $this->assertNotNull(
            \Yii::app()->user->getFlash($type),
            'Flash message not found.',
        );
    }

    /**
     * Expect that a controller method renders the given view
     */
    protected function assertControllerRendered(string $view): void
    {
        $this->assertEquals(
            $view,
            $this->viewFile,
            "Didn't render the expected '{$view}'' view.",
        );
    }

    /**
     * Expect that a controller method renders the view with the following param.
     */
    protected function assertControllerRenderedWithParam(string $paramName): void
    {
        $this->assertArrayHasKey(
            $paramName,
            $this->viewParams,
            "Didn't render with the expected '{$paramName}' param.",
        );
    }

    /**
     * Expect that a controller method renders a JSON with the following array.
     *
     * @param array<string, mixed> $array
     */
    protected function assertControllerRenderedJson(array $array, float $delta = 0.0001): void
    {
        $this->assertEqualsWithDelta($array, $this->jsonParam, $delta, 'Rendered a different JSON');
    }

    /**
     * Return the data to be rendered as JSON by the controller.
     */
    protected function getControllerJsonResponse(): mixed
    {
        return $this->jsonParam;
    }

    /**
     * Expect that a controller method renders the view with the following params.
     *
     * @param array<int, string> $paramsNames
     */
    protected function assertControllerRenderedWithParams(array $paramsNames): void
    {
        foreach ($paramsNames as $paramName) {
            $this->assertControllerRenderedWithParam($paramName);
        }
    }

    /**
     * Expect that a controller method rendirects to a given path
     */
    protected function assertControllerRedirectedTo(mixed $path): void
    {
        $this->assertEquals(
            $path,
            $this->redirectPath,
            "Didn't redirect to the given path.",
        );
    }

    /**
     * Expect that a controller method forwards to a given path
     */
    protected function assertControllerForwardedTo(mixed $path): void
    {
        $this->assertEquals(
            $path,
            $this->forwardPath,
            "Didn't redirect to the given path.",
        );
    }

    protected function getControllerRenderParam(string $paramName): mixed
    {
        return $this->viewParams[$paramName];
    }

    protected function getSessionMock(): FPhactorySessionMock
    {
        return new FPhactorySessionMock();
    }

    /**
     * Set HTTP request method name and params
     *
     * @param array<string, mixed> $bodyParams
     * @param array<string, mixed> $queryParams
     */
    protected function setRequest(string $method, array $bodyParams = [], array $queryParams = []): void
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
     *
     * @param array<string, mixed> $bodyParams
     * @param array<string, mixed> $queryParams
     */
    protected function setAjaxRequest(string $method, array $bodyParams = [], array $queryParams = []): void
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->setRequest($method, $bodyParams, $queryParams);
    }

    /**
     * Get next sequence value for a given model class
     */
    protected function getNextIdFor(string $modelClass): int
    {
        if (!class_exists($modelClass)) {
            throw new \RuntimeException('Model class does not exist: ' . $modelClass);
        }

        $model = $modelClass::model();

        if (!$model instanceof \CActiveRecord) {
            throw new \RuntimeException('Model method did not return CActiveRecord: ' . $modelClass);
        }

        $sequenceName = $model->getTableSchema()->sequenceName ?? '';

        return (int) \Yii::app()->db->getLastInsertID($sequenceName);
    }

    /**
     * @param array<int, non-empty-string> $methods
     */
    protected function getControllerMock(array $methods = []): MockObject&\CController
    {
        $className = $this->getControllerClassName();

        if (!class_exists($className)) {
            throw new \RuntimeException('Controller class does not exist: ' . $className);
        }

        if (!is_subclass_of($className, \CController::class)) {
            throw new \RuntimeException('Controller class must extend CController: ' . $className);
        }

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

        $onlyMethods = array_values(array_filter(
            array_merge($defaultMethods, $methods),
            static fn(string $method): bool => $method !== '',
        ));

        $mock = $this->createPartialMock($className, $onlyMethods, [$id]);

        if (!$mock instanceof \CController) {
            throw new \RuntimeException('Controller mock must be a CController instance: ' . $className);
        }

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
                $this->callback(function (mixed $params): bool {
                    if (!is_array($params)) {
                        return true;
                    }

                    $viewParams = [];

                    foreach ($params as $key => $value) {
                        if (is_string($key)) {
                            $viewParams[$key] = $value;
                        }
                    }

                    $this->viewParams = $viewParams;

                    return true;
                }),
            );
        $mock->expects($this->any())
            ->method('redirect')
            ->with($this->captureArg($this->redirectPath));
        $mock->expects($this->any())
            ->method('forward')
            ->with($this->captureArg($this->forwardPath));
        $mock->expects($this->any())
            ->method('filterAccessControl')
            ->with($this->captureArg($this->filterChain));
        $mock->expects($this->any())
            ->method('renderJson')
            ->with($this->captureArg($this->jsonParam));
        $mock->expects($this->any())
            ->method('setFilterId')
            ->with($this->captureArg($id));

        return $mock;
    }

    protected function getControllerId(): string
    {
        $id = preg_replace('/([A-Z])/', '_$1', $this->getControllerClassName()) ?? '';
        $id = preg_replace('/^_/', '', $id) ?? '';

        return strtolower($id);
    }

    /**
     * Parse and autoload class name based on the class path.
     */
    protected function getControllerClassName(): string
    {
        $className = $this->classPath;

        if (str_contains($className, '.')) {
            \Yii::import($className);
            $className = explode('.', $className);
            $className = array_pop($className);
        }

        if (!is_string($className) || $className === '') {
            throw new \RuntimeException('Controller class path is not configured');
        }

        return $className;
    }

    /**
     * Capture a method call argument
     *
     * @see http://stackoverflow.com/questions/7822036/phpunit-get-arguments-to-a-mock-method-call
     *
     * @return Callback<mixed>
     */
    protected function captureArg(mixed &$arg): Callback
    {
        return $this->callback(function ($argToMock) use (&$arg) {
            $arg = $argToMock;

            return true;
        });
    }

    /**
     * Verify if the current user has access to a given action
     */
    protected function expectAccessTo(string $action, bool $allowed = true): void
    {
        $this->assertFalse(
            empty($this->controller->accessRules()),
            'Controller does not have "accessRules".',
        );
        $filterMock = $this->getMockBuilder(\CAccessControlFilter::class)
            ->onlyMethods(['accessDenied'])
            ->getMock();
        $filterMock
            ->expects($allowed ? $this->never() : $this->once())
            ->method('accessDenied');
        $filterMock->setRules($this->controller->accessRules());
        $filterMock->filter(
            new \CFilterChain(
                $this->controller,
                $this->controller->createAction($action),
            ),
        );
        $this->controller->expects($this->any())->method('invalidActionParams');
        $this->controller->run($action);
    }
}
