<?php

namespace Weather\Router;

use Symlex\Router\RestRouter as SymlexRestRouter;

class RestRouter extends SymlexRestRouter
{
    use SessionTrait;
}