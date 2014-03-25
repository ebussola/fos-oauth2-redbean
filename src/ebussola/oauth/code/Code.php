<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 25/03/14
 * Time: 16:34
 */

namespace ebussola\oauth\code;


class Code implements \ebussola\oauth\Code, \OAuth2\Model\IOAuth2AuthCode {

    private $bean;

    public $id;
    public $code;
    public $redirect_uri;
    public $client_id;
    public $expires_in;
    public $has_expired;
    public $token;
    public $scope;
    public $data;

    public function __construct(\RedBean_OODBBean $bean) {
        $this->bean = $bean;

        $this->id = $bean->id;
        $this->code = $bean->code;
        $this->redirect_uri = $bean->redirect_uri;
        $this->client_id = $bean->client_id;
        $this->expires_in = $bean->expires_in;
        $this->has_expired = $bean->has_expired;
        $this->token = $bean->token;
        $this->scope = $bean->scope;
        $this->data = $bean->data;
    }

    public function getRedirectUri() {
        return $this->redirect_uri;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getExpiresIn() {
        return $this->expires_in;
    }

    public function hasExpired() {
        return $this->has_expired;
    }

    public function getToken() {
        return $this->token;
    }

    public function getScope() {
        return $this->scope;
    }

    public function getData() {
        return $this->data;
    }
    
    public function getBean() {
        $this->bean->id = $this->id;
        $this->bean->code = $this->code;
        $this->bean->redirect_uri = $this->redirect_uri;
        $this->bean->client_id = $this->client_id;
        $this->bean->expires_in = $this->expires_in;
        $this->bean->has_expired = $this->has_expired;
        $this->bean->token = $this->token;
        $this->bean->scope = $this->scope;
        $this->bean->data = serialize($this->data);

        return $this->bean;
    }
    
}