<?php

namespace Syntags\RouteBinder\Middlewares;


use Syntags\RouteBinder\Exceptions\RouteBinderException;
use App\User;
use Illuminate\Http\Request;

class BinderMiddleware
{
    /**
     * @param Request $request
     * @param $next
     * @return mixed
     * @throws RouteBinderException
     */
    public function handle(Request $request, $next){
        $route = $request->route();
        foreach ($request->route()[2] as $parameter => $bindingKey) {

            $class = $this->resolveClassName($parameter);

            if($route[2][$parameter] instanceof  $class){
                continue;
            }

            $route[2][$parameter] = $class::findOrFail($bindingKey);
            app()->bind(User::class, function() use ($class, $bindingKey){
                return $class::findOrFail($bindingKey);
            });
        }
        $request->setRouteResolver(function() use($route){
            return $route;
        });

        return $next($request);
    }

    /**
     * @param $parameter
     * @return mixed|string
     * @throws RouteBinderException
     */
    protected function resolveClassName($parameter){
        if(! is_null(config('modelbinder.'.$parameter)))
            if(class_exists(config('modelbinder.'.$parameter))) $class = config('modelbinder.'.$parameter);
            else if(class_exists('\App\\'.config('modelbinder.'.$parameter))) $class = '\App\\'.config('modelbinder.'.$parameter);
            else throw new RouteBinderException($parameter);
        else
            if(class_exists($parameter)) $class = $parameter;
            else if(class_exists(ucfirst($parameter))) $class = ucfirst($parameter);
            else if(class_exists('\App\\'.$parameter)) $class = '\App\\'.$parameter;
            else throw new RouteBinderException($parameter);

        return $class;
    }
}