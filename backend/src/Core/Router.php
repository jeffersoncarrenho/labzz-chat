<?php

class Router {

    private $routes = [];

    public function add($method,$path,$handler)
    {
        $this->routes[] = [$method,$path,$handler];
    }

    public function dispatch($method,$uri)
    {
        foreach($this->routes as $route){

            if($route[0] === $method && $route[1] === $uri){

                $handler = $route[2];
                return (new $handler[0])->{$handler[1]}();

            }

        }

        http_response_code(404);
        echo json_encode(["error"=>"not found"]);

    }

}