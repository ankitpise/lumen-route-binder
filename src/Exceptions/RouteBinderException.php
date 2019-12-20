<?php


namespace Syntags\RouteBinder\Exceptions;


use Throwable;

class RouteBinderException extends \Exception
{
    protected $message = "Failed to find model for ";
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = "Failed to find model with namespace $message";
    }
}