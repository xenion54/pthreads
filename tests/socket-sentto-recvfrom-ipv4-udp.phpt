--TEST--
Test if Socket::recvfrom() receives data sent by Socket::sendto() via IPv4 UDP
--CREDITS--
Copied from php/php-src and adjusted, originally created by 
Falko Menge <mail at falko-menge dot de>
PHP Testfest Berlin 2009-05-09
--FILE--
<?php
    $socket = new Socket(\Socket::AF_INET, \Socket::SOCK_DGRAM, \Socket::SOL_UDP);
    if (!$socket) {
        die('Unable to create AF_INET socket');
    }
    if (!$socket->setBlocking(false)) {
        die('Unable to set nonblocking mode for socket');
    }

    $address = '127.0.0.1';
    $socket->sendto('', 1, 0, $address); // cause warning
    if (!$socket->bind($address, 1223)) {
        die("Unable to bind to $address:1223");
    }
    var_dump($socket->recvfrom($buf, 12, 0, $from, $port)); // false
    
    $msg = "Ping!";
    $len = strlen($msg);
    $bytes_sent = $socket->sendto($msg, $len, 0, $address, 1223);
    if ($bytes_sent == -1) {
        die('An error occurred while sending to the socket');
    } else if ($bytes_sent != $len) {
        die($bytes_sent . ' bytes have been sent instead of the ' . $len . ' bytes expected');
    }

    $from = "";
    $port = 0;
    $socket->recvfrom($buf, 12, 0); // cause warning
    $socket->recvfrom($buf, 12, 0, $from); // cause warning
    $bytes_received = $socket->recvfrom($buf, 12, 0, $from, $port);
    if ($bytes_received == -1) {
        die('An error occurred while receiving from the socket');
    } else if ($bytes_received != $len) {
        die($bytes_received . ' bytes have been received instead of the ' . $len . ' bytes expected');
    }
    echo "Received $buf from remote address $from and remote port $port" . PHP_EOL;

    $socket->close();
--EXPECTF--

Warning: Wrong parameter count for Socket::sendto() in %s on line %d
bool(false)

Warning: Socket::recvfrom() expects at least 4 parameters, 3 given in %s on line %d

Warning: Wrong parameter count for Socket::recvfrom() in %s on line %d
Received Ping! from remote address 127.0.0.1 and remote port 1223
