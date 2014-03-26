<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 26/03/14
 * Time: 14:02
 */

namespace ebussola\oauth\redbean;


use ebussola\oauth\Client;
use OAuth2\Model\IOAuth2Client;

trait TraitClientImplementation {

    /**
     * Get a client by its ID.
     *
     * @param string $client_id
     *
     * @return IOAuth2Client | Client
     */
    public function getClient($client_id) {
        $client_bean = $this->redbean->load($this->tables['client'], $client_id);
        $client = new \ebussola\oauth\client\Client($client_bean);

        return $client;
    }

    /**
     * Make sure that the client credentials are valid.
     *
     * @param IOAuth2Client | Client $client
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

} 