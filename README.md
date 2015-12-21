# RoutingServiceProvider

Convention-based controllers for Silex

[![Latest Stable Version](https://poser.pugx.org/achrafsoltani/routing-service-provider/v/stable)](https://packagist.org/packages/achrafsoltani/routing-service-provider)
[![Total Downloads](https://poser.pugx.org/achrafsoltani/routing-service-provider/downloads)](https://packagist.org/packages/achrafsoltani/routing-service-provider)
[![License](https://poser.pugx.org/achrafsoltani/routing-service-provider/license)](https://packagist.org/packages/achrafsoltani/routing-service-provider)

Requirements
------------
 * PHP 5.3+
 * monolog/monolog (through the MonologServiceProvider)

Installation
------------ 
```sh
$ composer require achrafsoltani/routing-service-provider
```
Setup
------------
``` {.php}
$loader = require_once __DIR__.'/vendor/autoload.php';
// THIS LINE IS MANDATORY, SO THE AUTOLOAD BECOMES AWARE OF YOUR CUSTOM CONTROLLERS
$loader->addPsr4('',__DIR__.'/src/',true);

use Silex\Application;
use AchrafSoltani\Provider\RoutingServiceProvider;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();
$app['debug'] = true;

// Registering
$app->register(new RoutingServiceProvider());

// Defining routes
// You could also implement a custom routes loader from different locations and server a RouteCollection
// instance throough : $app['routing']->addRoutes($routes, $prefix);
$route = new Symfony\Component\Routing\Route('/', array('controller' => 'Foo\Controller\MainController::index'));
// setting methods is optional
$route->setMethods(array('GET', 'POST'));

$route2 = new Symfony\Component\Routing\Route('/hello', array('controller' => 'Foo\Controller\MainController::hello'));

$app['routing']->addRoute('home', $route);
$app['routing']->addRoute('hello', $route2);

// call this rigth before $app->run();
$app['routing']->route();
$app->run();
```

Example Controller
------------

``` {.php}
<?php

namespace Foo\Controller;

use AchrafSoltani\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

// you should extend the base Controller (AchrafSoltani\Controller\Controller) in order to have
// the service container injected
class MainController extends Controller
{
    public function index()
    {
        // services can be accessed as array params: $this->container['key'];
        // $this->container is equal to the $app instance
        if($this->container['debug'])
        {
            // or through the get method (symfony Like): $this->container['key'];
            return $this->get('twig')->render('form.html.twig');
        }
    }
    
    public function hello()
    {
        
        if($this->get('request')->isMethod('POST'))
        {
            $username = $this->get('request')->get('username');
            return $this->get('twig')->render('hello.html.twig', array('username' => $username));
        }
        
        return new Response('no post');
    }
}
```



