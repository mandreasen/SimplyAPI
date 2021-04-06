# Simply.com PHP API script #

## Description ##

This script provides a PHP interface to the [Simply.com REST API](https://www.simply.com/docs/api).

## Usage  ##

```php
require_once "Client.php";
$simply = new Simply\Client('accountname', 'apikey');
$products = $simply->getProducts();
var_dump($products);
```


## Information ##

This script was made for me by me, to have a simple way to use the [Simply.com REST API](https://www.simply.com/docs/api). There are no error handling in the client and response is data from the [Simply.com REST API](https://www.simply.com/docs/api).

Contributions are welcome. Sharing is caring.
