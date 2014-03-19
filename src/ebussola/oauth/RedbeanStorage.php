<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 19/03/14
 * Time: 11:22
 */

namespace ebussola\oauth;


use ebussola\oauth\accesstoken\AccessToken;
use \OAuth2\Model\IOAuth2AccessToken;
use OAuth2\IOAuth2Storage;
use OAuth2\Model\IOAuth2Client;

class RedbeanStorage implements IOAuth2Storage {

    const TABLE_CLIENTS = 'clients';
    const TABLE_ACCESS_TOKENS = 'accesstokens';

    /**
     * @var \RedBean_Facade
     */
    private $redbean;

    public function __construct(\RedBean_Facade $redbean) {
        $this->redbean = $redbean;
    }

    /**
     * Get a client by its ID.
     *
     * @param string $client_id
     *
     * @return IOAuth2Client|Client
     */
    public function getClient($client_id) {
        $client_bean = $this->redbean->load(self::TABLE_CLIENTS, $client_id);
        $client = new client\Client($client_bean);

        return $client;
    }

    /**
     * Make sure that the client credentials are valid.
     *
     * @param IOAuth2Client|Client $client
     * The client for which to check credentials.
     * @param string        $client_secret
     * (optional) If a secret is required, check that they've given the right one.
     *
     * @return
     * TRUE if the client credentials are valid, and MUST return FALSE if they aren't.
     * @endcode
     *
     * @see     http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-3.1
     *
     * @ingroup oauth2_section_3
     */
    public function checkClientCredentials(IOAuth2Client $client, $client_secret = null) {
        return $client->client_secret == $client_secret;
    }

    /**
     * Look up the supplied oauth_token from storage.
     *
     * We need to retrieve access token data as we create and verify tokens.
     *
     * @param string $oauth_token
     * The token string.
     *
     * @return IOAuth2AccessToken
     *
     * @ingroup oauth2_section_7
     */
    public function getAccessToken($oauth_token) {
        $access_token_bean = $this->redbean->findOne(self::TABLE_ACCESS_TOKENS, ' token = ? ', array($oauth_token));
        $access_token = new AccessToken($access_token_bean);

        return $access_token;
    }

    /**
     * Store the supplied access token values to storage.
     *
     * We need to store access token data as we create and verify tokens.
     *
     * @param string        $oauth_token
     * The access token string to be stored.
     * @param IOAuth2Client $client
     * The client associated with this refresh token.
     * @param mixed         $data
     * Application data associated with the refresh token, such as a User object.
     * @param int           $expires
     * The timestamp when the refresh token will expire.
     * @param string        $scope
     * (optional) Scopes to be stored in space-separated string.
     *
     * @ingroup oauth2_section_4
     */
    public function createAccessToken($oauth_token, IOAuth2Client $client, $data, $expires, $scope = null) {
        $access_token_bean = $this->redbean->dispense(self::TABLE_ACCESS_TOKENS);
        $access_token = new AccessToken($access_token_bean);

        $access_token->token = $oauth_token;
        $access_token->client_id = $client->getPublicId();
        $access_token->data = $data;
        $access_token->expires_in = $expires;
        $access_token->scope = $scope;

        $this->redbean->store($access_token->getBean());
    }

    /**
     * Check restricted grant types of corresponding client identifier.
     *
     * If you want to restrict clients to certain grant types, override this
     * function.
     *
     * @param IOAuth2Client $client
     * Client to check.
     * @param string        $grant_type
     * Grant type to check. One of the values contained in OAuth2::GRANT_TYPE_REGEXP.
     *
     * @return
     * TRUE if the grant type is supported by this client identifier, and
     * FALSE if it isn't.
     *
     * @ingroup oauth2_section_4
     */
    public function checkRestrictedGrantType(IOAuth2Client $client, $grant_type) {
        return true;
    }

}