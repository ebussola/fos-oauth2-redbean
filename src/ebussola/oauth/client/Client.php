<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 19/03/14
 * Time: 12:14
 */

namespace ebussola\oauth\client;


use OAuth2\Model\IOAuth2Client;

class Client implements IOAuth2Client, \ebussola\oauth\Client {

    protected $bean;

    public $id;
    public $redirect_uris;
    public $client_secret;

    public function __construct(\RedBean_OODBBean $bean) {
        $this->bean = $bean;

        $this->id = $bean->id;
        $this->redirect_uris = unserialize($bean->redirect_uris);
        $this->client_secret = $bean->client_secret;
    }

    public function getPublicId() {
        return $this->id;
    }

    public function getRedirectUris() {
        return $this->redirect_uris;
    }

    public function getBean() {
        $this->bean->redirect_uris = serialize($this->redirect_uris);
        $this->bean->client_secret = $this->client_secret;

        return $this->bean;
    }

}