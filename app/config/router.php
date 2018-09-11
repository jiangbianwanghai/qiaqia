<?php

$router = $di->getRouter();

$router->add(
    '/auth',
    [
        'controller' => 'index',
        'action'     => 'auth',
    ]
);
$router->add(
    '/logout',
    [
        'controller' => 'index',
        'action'     => 'logout',
    ]
);
$router->add(
    '/signup',
    [
        'controller' => 'index',
        'action'     => 'signup',
    ]
);
$router->add(
    '/signin',
    [
        'controller' => 'index',
        'action'     => 'signin',
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
    '/chatlogkf/{id:[\w]+}',
    [
        'controller' => 'index',
        'action'     => 'chatlogkf',
    ]
);
$router->add(
    '/chat/{uid:[\w]+}',
    [
        'controller' => 'index',
        'action'     => 'index',
    ]
);
$router->add(
    '/kh/live',
    [
        'controller' => 'index',
        'action'     => 'livekh',
    ]
);

$router->handle();
