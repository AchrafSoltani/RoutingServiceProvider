<?php

namespace AchrafSoltani\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Router
{
    private $app;
    private $context;
    private $request;
    private $routes;
    private $matcher;
    
    public function __construct(RequestContext $context, $app)
    {
        $this->app = $app;
        $this->context = $context;
        $this->request = Request::createFromGlobals();
        $this->routes = new RouteCollection(); 
    }
    
    public function addRoute($name, Route $route)
    {
        $this->routes->add($name, $route);
    }
    
    public function addRoutes(RouteCollection $collection, string $prefix = null)
    {
        $this->routes->addCollection($collection, $prefix);
    }
    
    public function route()
    {
        $this->app['routes']->addCollection($this->routes);
        
        $this->matcher = new UrlMatcher($this->routes, $this->context);
        
        try{
            $parameters = $this->matcher->match($this->request->getPathInfo()); 
            
            $_route = explode('::', $parameters['controller']);
            
            $class = $_route[0];
            $method = $_route[1];
            
            if (!class_exists($class)) {
                $app->abort(404);
            }
            
            $reflection = new \ReflectionClass($class);
            if (!$reflection->hasMethod($method)) {
                $app->abort(404);
            }
            
            $app = &$this->app;

            $this->app->match($this->request->getPathInfo(), function () use (&$app, $class, $method) 
            {
                $controller = new $class($app);
                return $controller->$method();
            })
            ->bind($parameters['_route']);
        }
        catch(ResourceNotFoundException $e)
        {
            
        }
        catch(MethodNotAllowedException $e)
        {
            
        }
    }
}