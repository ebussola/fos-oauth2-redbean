<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 19/03/14
 * Time: 12:14
 */

namespace ebussola\oauth;


use OAuth2\Model\IOAuth2Client;

class Client implements IOAuth2Client {

    protected $bean;

    public $id;
    public $redirect_uris;

    public function __construct(\RedBean_OODBBean $bean) {
        $this->bean = $bean;

        $this->id = $bean->id;
        $this->redirect_uris = unserialize($bean->redirect_uris);
    }

    public function getPublicId() {
        return $this->id;
    }

    public function getRedirectUris() {
        return $this->redirect_uris;
    }

    public function getBean() {
        $this->bean->redirect_uris = serialize($this->redirect_uris);

        return $this->bean;
    }

}