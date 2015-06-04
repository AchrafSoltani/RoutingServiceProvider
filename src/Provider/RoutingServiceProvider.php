<?php
/**
 * RoutingServiceProvider
 *
 * OO Controller based routing for Silex
 *
 * @package		AchrafSoltani\Provider;
 * @author		Achraf Soltani <achraf.soltani@gmail.com>
 * @date        05/29/2015
 * @file        RoutingServiceProvider.php
 */
 
namespace AchrafSoltani\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use AchrafSoltani\Routing\Router;

/**
 * Class RoutingServiceProvider
 * @package AchrafSoltani\Provider
 */
class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['routing'] = $app->share(function () use ($app) 
        {
            return new Router($app['request_context'], $app);
        });
    }
    
    public function boot(Application $app)
    {
        
    }
}