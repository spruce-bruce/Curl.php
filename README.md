Curl.php
========

This is a wrapper class for curl that I've developed as a Curl shorthand. There are other curl wrappers out there, but I was never really a fan of any of the ones I found.

In this class I implement static methods for the HTTP requests: get(), post(), delete(), and put().

## Installation
You can just download the zip of this repo (or clone it) and include the Curl.php file. If you like composer I've added this repo to packagist and you can install by adding `"spruce-bruce/composer" : "0.1.*"` to your requirements.

## Usage
Here are some fictional examples that show the use of Curl.php.

### POST
Example POST:
```php
//set the url
$url = "http://somefictionaldomain.com/api";

//set the POST vars
$data = array(
    'query' => 'active',
    'page' => 3,
    'count' => 10
);

//get the post response
$response = Curl::post($url, $data);
```

### GET
Example GET with additional curl options:
```php
//set the url
$url = "http://somefictionaldomain.com/api";

//set the GET vars
$data = array(
    'query' => 'active',
    'page' => 3,
    'count' => 10
);

//set addtional curl options
$options = array(
    CURLOPT_FAILONERROR => true
);

//get the GET response
$response = Curl::get($url, $data, $options);
```

## Default curl options
Each type of http request will have some default curl options set. Defaults cannot currently be changed. Defaults are determined by my experience using curl in the past, ie, they are the options that I will generally always set for a given HTTP request type.

### POST
- CURLOPT_RETURNTRANSFER
- CURLOPT_POST
- CURLOPT_POSTFIELDS (only if the $data param is set)

### GET
- CURLOPT_RETURNTRANSFER

### DELETE
unimplemented

### PUT
unimplemented

## TODO
- Implemente delete() and put() methods
- Clean up HTTP code parsing and make it more robust (ie, add any messages in the response to the exception)
- Save and make response headers accessible
- Save and make curl resource accessible
- Document exceptions