# Soft1 Web Services PHP SDK

A plugin that simplifies the use of SoftOne WebServices.

## Installation
Install with Composer

```bash
composer require long-blade/softone-sdk
```

## Documentation

### - Initialization
First you need to set minimum require credentials for the api client.

```php
use SoftOne\Context;

// This can be set on the bootstrap of the application.
Context::initialize(
    'https://excample.com/s1services', // server endpoint
    'username',
    'password',
    '2001' // appid
);
```

In case you know all the parameters.

```php
use SoftOne\Context;

// This can be set on the bootstrap of the application.
Context::initialize(
    'https://excample.com/s1services', // server endpoint
    'username',
    'password',
    '2001', // appid
     #--- optional ---
    '1000', // company
    '1000', // branch
    '0', // module
    '1', // refId
);
```

Once the client is initialized, you can then create a service, and use it to communicate with the api

### - Reading
Fetching data from ERP can be achieved with a get request using BrowserInfo method constructing a simple query

```php
use SoftOne\Services\BrowserInfo;

$query = BrowserInfo::find()
       ->forObject('CUSTOMER') // This is like a table name
       ->withList('custom-list-filter') // use a custom erp soft one list filter
       ->where(['CUSTOMER.EMAIL'=> 'customer@company.gr']) // you can add filters like this
       ->limit(1);
```
Now that we have a query constructed, we can execute a simple get request.

```php
use SoftOne\Client;

$response = Client::get($foo)
```

### - Setting data
There are two methods available for setting data, `insert()` & `update()`

**Insert the data of a record in a Business Object**
```php
use SoftOne\Services\Data;

// Insert a new customer
$request = Data::insert('CUSTOMER',[
            'NAME' => 'TEST Soft One Technologies S.A.',
            'AFM' => '999863881',
            'EMAIL' => 'johng@softone.gr',
            'PHONE01' => '+302109484797',
            'FAX' => '9484094',
            'ADDRESS' => '6 Poseidonos street',
            'ZIP' => '17674',
        ]);

$customer = Client::get($request);

$customer->id // returns the new created customer id
```

**Modify the data of a record in a Business Object identified by a KEY**
```php
use SoftOne\Services\Data;

// Update a new customer with the id of 47
$request = Data::update('CUSTOMER', '47', [
            'NAME' => 'TEST Soft One Technologies S.A.',
            'AFM' => '999863881',
            'EMAIL' => 'johng@softone.gr',
            'PHONE01' => '+302109484797',
            'FAX' => '9484094',
            'ADDRESS' => '6 Poseidonos street',
            'ZIP' => '17674',
        ]);

$customer = Client::get($request);

$customer->isSuccess() // true if updated
```

### - Response
*The response implements the `SoftOne\Contracts\SoftoneResponseInterface`.*

```php
$response->isSuccess(); // Returns bool

$response->body(); // Returns an array with all the data,
// like so:
$body = [
    'reqID' => 0815969338959806593,
    'totalcount' => 6
    'fields' = [
        [
            'name' => 'CUSTOMER.EMAIL',
            'type' => 'string',
        ],
        // ...
    ],
    'columns' = [
        [
            'dataIndex' => 'CUSTOMER.EMAIL',
            'header' => '',
            'width' => '120',
            'decs' => '-1',
            'hidden' => '',
            'sortable' => '1',
        ],
        // ...
    ],
    'extrainfo' = [
        // ...
    ],
    'rows' = [ // the data according to each col
        [
            'email@excample.com',
            // ...
        ],
        // ...
    ]
];
```

**The response array can be filtered by using $response->data(['key1', 'key2',....]); method**

```php
$response->data(['columns', 'rows']);

//OR access explicitly to response obj
$response->rows
$response->columns
```

## Testing
Run tests with composer

```bash
composer test
```
