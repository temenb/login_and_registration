<?php

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use Core\Router;

class RouterTest extends TestCase
{
    public function testNewRouter()
    {
        $router = new Router();
        $defaultRoute = 'route2';
        $config = array(
            'controller1' => array(
                'route1' => 'action1',
                $defaultRoute => 'action2',
            ),
            'controller2' => array(
                'route3' => 'action1',
            ),
        );

        $router->setConfig($config, $defaultRoute);
        $this->assertInstanceOf('Core\\Router', $router);
        return $router;
    }

    public function testSetConfig()
    {
        $router = new Router();
        $originalConfig = array(
            'controller1' => array(
                'route1' => 'action1',
                'route2' => 'action2',
            ),
            'controller2' => array(
                'route3' => 'action1',
                'route1' => 'action3',
            ),
            0 => 'notValid1',
            'notValid2' => array(),
        );
        $router->setConfig($originalConfig, 'route1');
        $reverseConfig = array(
            'controller1' => array(
                'action2' => 'route2',
            ),
            'controller2' => array(
                'action1' => 'route3',
                'action3' => 'route1',
            )
        );

        $config = array(
            'route1' => array(
                'controller' => 'controller2',
                'action' => 'action3'
            ),
            'route2' => array(
                'controller' => 'controller1',
                'action' => 'action2'
            ),
            'route3' => array(
                'controller' => 'controller2',
                'action' => 'action1'
            ),
        );
        $this->assertEquals($config, $router->getConfig());
        $this->assertEquals($reverseConfig, $router->getReverseConfig());
    }

    /**
     * @depends testNewRouter
     */
    public function testGetControllerAction(Router $router)
    {
        unset($_REQUEST[Router::REQUEST_PARAM]);
        $expected = array(
            'controller' => 'controller1',
            'action' => 'action2'
        );
        $this->assertEquals($expected, $router->getControllerAction());

        $expected = array(
            'controller' => 'controller2',
            'action' => 'action1'
        );
        $this->assertEquals($expected, $router->getControllerAction('route3'));

        $_REQUEST[Router::REQUEST_PARAM] = 'route3';
        $this->assertEquals($expected, $router->getControllerAction());

        $this->assertFalse($router->getControllerAction('notValidRoute'));
    }

    /**
     * @depends testNewRouter
     */
    public function testGetRelativeUrl(Router $router)
    {
        $expected = 'route3';
        $this->assertEquals($expected, $router->getRequestParhValue('controller2', 'action1'));
    }

    /**
     * @depends testNewRouter
     */
    public function testSetDefaultRoute(Router $router)
    {
        $expected = 'route2';
        $this->assertEquals($expected, $router->getRequestParhValue('foo', 'bar'));
    }

    /**
     * @depends testNewRouter
     */
    public function testSetDefaultRouteException(Router $router)
    {
        $this->expectException('\\Exception');
        $router->setDefaultRoute('abra-cadabra');
    }
}
