<?php
    require __DIR__ . '/vendor/autoload.php';

    use Thruway\ClientSession;
    use Thruway\Peer\Client;
    use Thruway\Transport\PawlTransportProvider;

    $client = new Client('chat');
    $client->addTransportProvider(new PawlTransportProvider('ws://127.0.0.1:8080/'));
    $client->on('open', function (ClientSession $session) {
        // 1) subscribe to a topic
        $onevent = function ($args) {
            echo "Event {$args[0]}\n";
        };
        $session->subscribe('studip.chat', $onevent);
        // 2) publish an event
        $session->publish('studip.chat', ['Hello, world from PHP!!!'], [], ['acknowledge' => true])->then(
            function () {
                echo "Publish Acknowledged!\n";
            },
            function ($error) {
                // publish failed
                echo "Publish Error {$error}\n";
            }
        );
    });
    $client->on('close', function (ClientSession $session) {
        $session->publish('studip.chat', ['See ya!'], [], ['acknowledge' => true]);
    });
    $client->start();
