Mess
====

Mess is a signed message encoder/decoder class useful for secure data passing to HTML forms or AJAX API calls.

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/CaffeinaLab/Mess/badges/quality-score.png?s=bb13f317db1553b085b3fc4b3b65521fd8170a66)](https://scrutinizer-ci.com/g/CaffeinaLab/Mess/)

```php
// Start Mess by passing a user secret code for securing origin of payloads
Mess::init(array(
  'secret' => 'this_is_my_secret',
));

// Test data
$data_to_send = "Some data.";

// Encode data into an URL-secure payload
// You can pass this to a get/post request or into a form input tag
$payload = Mess::pack($data_to_send);
```

**$payload** :
```
eNodyMkNgzAQBdBWoikg8iyyPUMZqeB74RblXDA3RO9ATk96CIljCw4atNymoM_vO18DO97_CdoeswVZ8wLLuhpcJ6ueBL1PkVZNWvE1oxiLc5csWg0KFa_DCpiVByYt5wWdkR2c
```

```php
// If we want to retrieve data from a received payload :
$data_received = Mess::parse($payload);
```

**$data_received** :
```
Some data.
```
