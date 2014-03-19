<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 19/03/14
 * Time: 14:33
 */

namespace ebussola\oauth\accesstoken;


class AccessToken implements \OAuth2\Model\IOAuth2AccessToken, \ebussola\oauth\AccessToken {

    protected $bean;

    public $id;
    public $client_id;
    public $expires_in;
    public $has_expired;
    public $token;
    public $scope;
    public $data;

    public function __construct(\RedBean_OODBBean $bean) {
        $this->bean = $bean;

        $this->id = $bean->id;
        $this->client_id = $bean->client_id;
        $this->expires_in = $bean->expires_in;
        $this->has_expired = $bean->has_expired;
        $this->token = $bean->token;
        $this->scope = $bean->scope;
        $this->data = unserialize($bean->data);
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
        $this->bean->client_id = $this->client_id;
        $this->bean->expires_in = $this->expires_in;
        $this->bean->has_expired = $this->has_expired;
        $this->bean->token = $this->token;
        $this->bean->scope = $this->scope;
        $this->bean->data = serialize($this->data);

        return $this->bean;
    }

}