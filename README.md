Curl.php
========

This is a wrapper class for curl that I've developed as a Curl shorthand. There are other curl wrappers out there, but I was never really a fan of any of the ones I found.

In this class I implement static methods for the HTTP requests: get(), post(), delete(), and put().

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
