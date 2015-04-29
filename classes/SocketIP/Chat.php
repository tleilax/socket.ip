<?php
namespace SocketIP;
use Ratchet\ConnectionInterface as ConnectionInterface;

class Chat implements \Ratchet\Wamp\WampServerInterface
{
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        $topic_id = $topic->getId();
        echo "PUBLISHED: Connection {$conn->resourceId}\n on topic {$topic_id}";

        $topic->broadcast($event);
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $topic_id = $topic->getId();
        echo "CALLED: Connection {$conn->resourceId} on topic {$topic_id}\n";

        $conn->callError($id, $topic, 'RPC not supported on this demo');
    }
    
    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        echo "SUBSCRIBED: Connection {$conn->resourceId}\n";
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        echo "UNSUBSCRIBED: Connection {$conn->resourceId}\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "ATTACHED: Connection {$conn->resourceId}\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        echo "DETACHED: Connection {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "ERROR: Connection {$conn->resourceId}\n";
    }
}
