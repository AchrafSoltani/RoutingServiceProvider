<?php

namespace AchrafSoltani\Controller;

use Silex\Application;

class Controller
{
    protected $container;
    
    public function __construct(Application $app)
    {
        $this->container = $app;
    }
    
    public function get($service)
    {
        return $this->container[$service];
    }
}