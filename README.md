# Shover

A lightweight PHP interface for the [Pusher API](http://pusher.com/docs/rest_api).

## Example

``` php
<?php
use Tackk\Shover\Credentials;
use Tackk\Shover\Connection;
use Tackk\Shover\Client;

require 'vendor/autoload.php';

$credentials = new Credentials('3', '278d425bdf160c739803', '7ad3773142a6692b25b8');
$connection = new Connection($credentials);
$client = new Client($connection);

$client->trigger('test_channel', 'send', ['message' => 'test']);
```
