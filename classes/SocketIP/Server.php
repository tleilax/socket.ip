<?php
namespace SocketIP;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Server implements MessageComponentInterface
{
    protected $clients;
    
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }
    
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "ATTACHED: Connection {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $count = count($this->clients) - 1;
        echo "MESSAGE: Connection {$from->resourceId} to {$count} receiver(s)\n";
        echo "- {$msg}\n";

        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "DETACHED: Connection {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
        echo "CLOSED: Connection {$conn->resourceId}\n";
    }
}
