# Pushit

A simple PHP interface for the [Pusher API](http://pusher.com/docs/rest_api).

## Example

``` php
<?php

use Tackk\Pushit\Connection;
use Tackk\Pushit\Client;

require 'vendor/autoload.php';

$client = new Client(new Connection('3', '278d425bdf160c739803', '7ad3773142a6692b25b8'));

$client->trigger('test_channel', 'send', ['message' => 'test']);
```
