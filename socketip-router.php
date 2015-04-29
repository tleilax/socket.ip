<?php
    use Thruway\Peer\Router;
    use Thruway\Transport\RatchetTransportProvider;

    require __DIR__ . '/vendor/autoload.php';

    $loop = \React\EventLoop\Factory::create();

    $router = new Router($loop);
    $router->registerModule(new RatchetTransportProvider('127.0.0.1', 8080));
    $router->start();