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
            $this->request->attributes->add($this->matcher->match($this->request->getPathInfo()));
            $parameters = $this->matcher->match($this->request->getPathInfo());
            $this->app['request'] = $this->request;
            
            $_route = explode('::', $parameters['controller']);
            
            $class = $_route[0];
            $method = $_route[1];
            
            if (!class_exists($class)) {
                throw new ResourceNotFoundException('Class ' . $class . ' is not defined.');
            }
            
            $reflection = new \ReflectionClass($class);
            if (!$reflection->hasMethod($method)) {
                throw new ResourceNotFoundException('Class ' . $class . ' has no ' . $method . 'method defined.');
            }
            
            $app = &$this->app;
            $request = &$this->request;

            $this->app->match($this->request->getPathInfo(), function () use (&$app, $class, $method, &$request) 
            {
                $controller = new $class($app);
                return $controller->$method($request);
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