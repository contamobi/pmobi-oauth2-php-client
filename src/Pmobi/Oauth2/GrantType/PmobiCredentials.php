<?php
/**
 * conta.MOBI S/A
 *
 * @link http://conta.mobi/
 * @license MIT
 */

namespace Pmobi\Oauth2\GrantType;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use kamermans\OAuth2\GrantType\GrantTypeInterface;
use kamermans\OAuth2\Signer\ClientCredentials\SignerInterface;
use kamermans\OAuth2\Utils\Collection;
use kamermans\OAuth2\Utils\Helper;

/**
 * PmobiCredentials
 *
 * @package Pmobi\Oauth2\Oauth2\GrantType
 */
class PmobiCredentials implements GrantTypeInterface
{
    /**
     * Content-Type
     */
    const CONTENT_TYPE = 'application/json';

    /**
     * Token grant type
     */
    const GRANT_TYPE = 'password';

    /**
     * Token scope
     */
    const TOKEN_SCOPE = 'api';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Collection
     */
    private $config;

    /**
     * @var array
     */
    private static $configRequired = [
        'client_id',
        'client_secret',
        'username',
        'password',
    ];

    /**
     * PmobiCredentials constructor
     *
     * @param ClientInterface $client
     * @param array $config
     */
    public function __construct(ClientInterface $client, array $config)
    {
        $this->client = $client;
        $this->config = Collection::fromConfig($config, [], self::$configRequired);
    }

    /**
     * {@inheritdoc}
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRawData(
        SignerInterface $clientCredentialsSigner,
        $refreshToken = null
    ): array {
        if (Helper::guzzleIs('<', '6')) {
            throw new \RuntimeException(
                'GuzzleHttp 6 or newer required'
            );
        }

        $request = (
                new Request(
                    'POST',
                    $this->client->getConfig()['base_uri']
                )
            )
            ->withBody(
                $this->getPostBody()
            )
            ->withHeader('Content-Type', self::CONTENT_TYPE)
        ;

        $request = $clientCredentialsSigner->sign(
            $request,
            $this->config['client_id'],
            $this->config['client_secret']
        );

        $response = $this->client->send($request);

        return json_decode($response->getBody(), true);
    }

    /**
     * @return Stream
     */
    protected function getPostBody(): Stream
    {
        $data = [
            'grant_type' => self::GRANT_TYPE,
            'scope' => self::TOKEN_SCOPE,
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
        ];

        return \GuzzleHttp\Psr7\stream_for(json_encode($data));
    }
}
