OOReq
=====
A object oriented wrapper for CURL. (Not stable - WIP)

Theory of operation
===================
Using OOReq contains of 2 parts. First preparing the request and second transforming
the response to the desired format. 

1. Preparing the request
    ```php
    <?php
    $Request = new \OOReq\Request(new \OOReq\URL('http://example.com'));
    ```
    That is just a GET Request to http://example.com.
2. Transforming the result 
    ```php
    <?php

    $File = $Request->getResponseAs(new \OOReq\Response\File());
    ```
    That example just returns the response using the file response. The File
    class is an extension of the \SplFile class which implements the 
    createableByRequest Interface.  

Examples
----
Perform a GET Request and return the result as string.

```php
<?php
use OOReq\Request;
use OOReq\Url;
use OOReq\Response\String;

# Default method is GET
$Request = new Request(new Url('http://www.example.com'));

$result = $Request->resultAs(new String());
```

Return the result as File.
```php
<?php
use OOReq\Request;
use OOReq\Url;
use OOReq\Response\File;

# Default method is GET
$Request = new Request(new Url('http://www.example.com'));

$File = $Request->resultAs(new File('/tmp/myFile'));
```

Perform a POST Request.

```php
<?php
use OOReq\Request;
use OOReq\Url;
use OOReq\HTTPMethod\POST;
use OOReq\Payload;
use OOReq\DataAsPOST;
use OOReq\Response\String;

$Url = new Url('http://www.somewhere.url');

# Preparing the payload.
$Data = new Payload(
                    new DataAsPOST('PostFieldA', 'ValueA'),
                    new DataAsPOST('PostFieldB', 'ValueB')
);

$Request = new Request($Url, new POST(), $Data);
$result = $Request->resultAs(new String());

```


Why should you use it?
======================
Of course you could just use curl() or the other build in functions.
```php
<?php
$result=file_get_contents('http://www.example.com');
```
This does the same as the first GET example and is much simpler. But what happens if you wish
to test your code?

```php
<?php

class getSth
{
    private $url;
    
    public function __construct($url)
    {
        $this->url=$url;
    }

    public function performRequest($param)
    {
        $result=file_get_contents($this->url.'/param='.urlencode($param));
        # 
        # Here comes some error handling
        #
        return $result;
    }
}
```
In this case you have to setup an webserver to test your error handling. For automated testing this is
really annoying.
How to avoid this? Take a look at the following Example

```php
<?php

use OOReq\RequestInterface;
use OOReq\Url;
use OOReq\Payload;
use OOReq\DataAsGET;
use OOReq\Response\StringValue;

class getSth
{
    private $Request;
    private $Url;
     
    public function __construct(RequestInterface $Request, Url $Url)
    {
        $this->$Request;
        $this->Url = $Url;
    }

    public function performRequest($param)
    {
        $Payload = new Payload(new DataAsGet('param', $param));
        $Request = $this->Request->newGET($this->Url, $Payload); 
        $result = $Request->resultAs(new StringValue());
        # 
        # Here comes some error handling
        #
        return $result;
    }
}
```
In this case you just have to mock the request object and define the result of
the methods new(), which should return itself, and resultAs().

Another reason is that OOReq encourages you to split the creation of the request
and the interpretation of the result. That leads to smaler, easier to test 
classes.
