<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 26/03/14
 * Time: 13:58
 */

namespace ebussola\oauth\redbean;


use ebussola\oauth\code\Code;
use OAuth2\IOAuth2GrantCode;
use OAuth2\Model\IOAuth2AuthCode;
use OAuth2\Model\IOAuth2Client;

class GrantCode extends Storage implements IOAuth2GrantCode {

    /**
     * Fetch authorization code data (probably the most common grant type).
     *
     * Retrieve the stored data for the given authorization code.
     *
     * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
     *
     * @param string $code
     * The authorization code string for which to fetch data.
     *
     * @return IOAuth2AuthCode
     *
     * @see     http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.1
     *
     * @ingroup oauth2_section_4
     */
    public function getAuthCode($code) {
        $code_bean = $this->redbean->findOne($this->tables['code'], 'code = ?', [$code]);
        $code = new Code($code_bean);

        return $code;
    }

    /**
     * Take the provided authorization code values and store them somewhere.
     *
     * This function should be the storage counterpart to getAuthCode().
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
     *
     * @param string        $code
     * Authorization code string to be stored.
     * @param IOAuth2Client $client
     * The client associated with this authorization code.
     * @param mixed         $data
     * Application data to associate with this authorization code, such as a User object.
     * @param string        $redirect_uri
     * Redirect URI to be stored.
     * @param int           $expires
     * The timestamp when the authorization code will expire.
     * @param string        $scope
     * (optional) Scopes to be stored in space-separated string.
     *
     * @ingroup oauth2_section_4
     */
    public function createAuthCode($code_str, IOAuth2Client $client, $data, $redirect_uri, $expires, $scope = null) {
        $code_bean = $this->redbean->dispense($this->tables['code']);
        $code = new Code($code_bean);

        $code->code = $code_str;
        $code->client_id = $client->getPublicId();
        $code->data = $data;
        $code->redirect_uri = $redirect_uri;
        $code->expires_in = $expires;
        $code->has_expired = false;

        $this->redbean->store($code->getBean());
    }

    /**
     * Marks auth code as expired.
     *
     * Depending on implementation it can change expiration date on auth code or remove it at all.
     *
     * @param string $code
     */
    public function markAuthCodeAsUsed($code) {
        $code = $this->getAuthCode($code);
        $this->redbean->trash($code->getBean());
    }

}