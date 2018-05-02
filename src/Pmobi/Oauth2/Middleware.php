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
use kamermans\OAuth2\Persistence\FileTokenPersistence;
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
        $missingArgs = array_diff(self::$configRequired, array_keys($config));

        if ($missingArgs) {
            throw new \InvalidArgumentException(
                'Config is missing the following keys: ' . implode(', ', $missingArgs)
            );
        }

        $tokenUrl = $config['token_url'];
        unset($config['token_url']);

        $reauthClient = new Client([
            'base_uri' => $tokenUrl,
        ]);

        $grantType = new PmobiCredentials($reauthClient, $config);
        $oauth = new OAuth2Middleware($grantType);

        if (isset($config['token_filepath'])) {
            $tokenFilepath = $config['token_filepath'];
            unset($config['token_filepath']);

            $tokenPersistence = new FileTokenPersistence($tokenFilepath);
            $oauth->setTokenPersistence($tokenPersistence);
        }

        $stack = HandlerStack::create();
        $stack->push($oauth);

        return $stack;
    }
}
