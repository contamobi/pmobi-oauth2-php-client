### pague.MOBI Oauth2 Middleware

This is GuzzleHttp Middleware for pague.MOBI Oauth2. Based on [Guzzle OAuth 2.0 Subscriber](https://github.com/kamermans/guzzle-oauth2-subscriber).

##### Usage

With classes
```php
<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\OAuth2Middleware;
use Pmobi\Oauth2\GrantType\PmobiCredentials;

$authClient = new Client([
    "base_uri" => "https://anything.p.mobi/oauth2/token",
]);

$authConfig = [
    "client_id" => "your-client-id",
    "client_secret" => "your-client-secret",
    "username" => "your-username",
    "password" => "your-password",
];

$grantType = new PmobiCredentials($authClient, $authConfig);
$oauth = new OAuth2Middleware($grantType);
$stack = HandlerStack::create();
$stack->push($oauth);

$client = new Client([
    'handler' => $stack,
    'auth' => 'oauth',
]);

$response = $client->request(
    'get',
    'https://anything.p.mobi/anywhere',
    [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]
);

var_dump($response->getBody()->getContents());
```

With Pmobi Middleware
```php
<?php

use GuzzleHttp\Client;
use Pmobi\Oauth2\Middleware as PmobiOauth2Middleware;

$authConfig = [
    "token_url" => "https://anything.p.mobi/oauth2/token",
    "client_id" => "your-client-id",
    "client_secret" => "your-client-secret",
    "username" => "your-username",
    "password" => "your-password",
];

// Optional
$authConfig["token_filepath"] = "/tmp/access_token.json";

$stack = PmobiOauth2Middleware::createFromConfig($reauthConfig);

$client = new Client([
    'handler' => $stack,
    'auth' => 'oauth',
]);

$response = $client->request(
    'get',
    'https://anything.p.mobi/anywhere',
    [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]
);

var_dump($response->getBody()->getContents());
```

Token persistence
```php
<?php

use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\Persistence\FileTokenPersistence;

/* ... */

$tokenPersistence = new FileTokenPersistence("/tmp/access_token.json");

$oauth = new OAuth2Middleware($grantType);
$oauth->setTokenPersistence($tokenPersistence);

```

More information about persistence in [https://github.com/kamermans/guzzle-oauth2-subscriber#access-token-persistence](https://github.com/kamermans/guzzle-oauth2-subscriber#access-token-persistence). 