# Soft1 Web Services PHP SDK

___
A plugin that simplifies the use of SoftOne WebServices.

## Installation

___
Install with Composer

```bash
composer require long-blade/softone-sdk
```

## Documentation
___

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
Most of the time what you really need is to simply make a `get` request in order to find some ERP data.
For that, we can construct a simple find query by using the `BrowserInfo` method.

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

### - Response
*After performing a `get` request by using the `SoftOne\Client`, in return we get a `SoftOne\Http\Response` that 
implements the `SoftOneResponseInterface`.*

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

**You can filter down the response array, picking only the wanted keys, by using the `$response->data(['rows', 'columns']);` method.**

## Testing
___
Install with Composer

```bash
composer test
```
