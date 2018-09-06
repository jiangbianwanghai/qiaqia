<?php

$router = $di->getRouter();

$router->add(
    '/login',
    [
        'controller' => 'index',
        'action'     => 'login',
    ]
);
$router->add(
    '/chatlog/{id:[\w]+}',
    [
        'controller' => 'index',
        'action'     => 'chatlog',
    ]
);
$router->add(
    '/chat/{uid:[\w]+}',
    [
        'controller' => 'index',
        'action'     => 'index',
    ]
);

$router->handle();
