<?php
/**
 * conta.MOBI S/A
 *
 * @link http://conta.mobi/
 * @license MIT
 */

namespace Pmobi\Oauth2;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\OAuth2Middleware;
use Pmobi\Oauth2\GrantType\PmobiCredentials;

/**
 * Middleware
 *
 * @package Pmobi\Oauth2\Oauth2
 */
class Middleware
{
    /**
     * @var array
     */
    private static $configRequired = [
        'token_url',
        'client_id',
        'client_secret',
        'username',
        'password',
    ];

    /**
     * @param array $config
     * @return HandlerStack
     */
    public static function createFromConfig(array $config): HandlerStack
    {
        $missing = array_diff(self::$configRequired, array_keys($config));

        if ($missing) {
            throw new \InvalidArgumentException(
                'Config is missing the following keys: ' . implode(', ', $missing)
            );
        }

        $tokenUrl = $config['token_url'];
        unset($config['token_url']);

        $reauthClient = new Client([
            'base_uri' => $tokenUrl,
        ]);

        $grantType = new PmobiCredentials($reauthClient, $config);
        $oauth = new OAuth2Middleware($grantType);

        $stack = HandlerStack::create();
        $stack->push($oauth);

        return $stack;
    }
}
