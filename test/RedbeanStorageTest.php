<?php
use ebussola\oauth\redbean\GrantCode;

/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 19/03/14
 * Time: 12:21
 */

class RedbeanStorageTest extends PHPUnit_Framework_TestCase {

    /**
     * @var GrantCode
     */
    private $redbean_storage;

    /**
     * @var RedBean_Facade
     */
    private $redbean;

    private $tables;

    public function setUp() {
        $this->redbean = new RedBean_Facade();
        $this->redbean->setup('sqlite::memory:');

        $this->tables = array(
            'client'       => 'clients',
            'access_token' => 'accesstokens',
            'code'         => 'codes'
        );
        $this->redbean_storage = new GrantCode($this->redbean, $this->tables);
    }

    public function testGetClient() {
        $client_bean = $this->redbean->dispense($this->tables['client']);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $client->id = 1;
        $client->redirect_uris = array('localhost');
        $client->client_secret = '823984y9ncy9ny4hh284c823';
        $this->redbean->store($client->getBean());

        $client = $this->redbean_storage->getClient($client_bean->id);
        $this->assertInstanceOf('\ebussola\oauth\Client', $client);
        $this->assertEquals(array('localhost'), $client->redirect_uris);
    }

    public function testCheckClientCredentials() {
        $client_bean = $this->redbean->dispense($this->tables['client']);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $client->redirect_uris = array('localhost');
        $client->client_secret = 'shhhh_this_is_secret';

        $this->assertFalse($this->redbean_storage->checkClientCredentials($client, 'something'));
        $this->assertTrue($this->redbean_storage->checkClientCredentials($client, 'shhhh_this_is_secret'));
    }

    public function testGetAccessToken() {
        $access_token_bean = $this->redbean->dispense($this->tables['access_token']);
        $access_token = new \ebussola\oauth\accesstoken\AccessToken($access_token_bean);
        $access_token->client_id = 1;
        $access_token->expires_in = 3600;
        $access_token->has_expired = false;
        $access_token->token = md5('token');
        $access_token->scope = 'read,write';
        $access_token->data = array();
        $this->redbean->store($access_token->getBean());

        $access_token = $this->redbean_storage->getAccessToken(md5('token'));
        $this->assertInstanceOf('\ebussola\oauth\AccessToken', $access_token);
        $this->assertFalse($access_token->hasExpired());
    }

    /**
     * Should return an expired AccessToken
     */
    public function testGetAccessTokenWithInvalidToken() {
        $access_token = $this->redbean_storage->getAccessToken(md5('invalid_token'));
        $this->assertTrue($access_token->hasExpired());
    }

    public function testCreateAccessToken() {
        $client_bean = $this->redbean->dispense($this->tables['client']);

        $client = new \ebussola\oauth\client\Client($client_bean);
        $data = array(
            'xpto' => 'blah'
        );
        $expires = time() + 3600;
        $scope = 'read write';
        $token = md5('createaccesstoken');
        $this->redbean_storage->createAccessToken($token, $client, $data, $expires, $scope);

        $access_token = $this->redbean_storage->getAccessToken($token);
        $this->assertEquals($access_token->getClientId(), $client->id);
        $this->assertEquals($access_token->getData(), $data);
        $this->assertEquals($access_token->getExpiresIn(), $expires);
        $this->assertEquals($access_token->getScope(), $scope);
        $this->assertEquals($access_token->getToken(), $token);
    }

    public function testCreateAuthCode() {
        $client_bean = $this->redbean->dispense($this->tables['code']);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $code = md5(uniqid(time()));
        $data = [];
        $redirect_uri = 'http://google.com';
        $expires = time() + 3600;
        $this->redbean_storage->createAuthCode($code, $client, $data, $redirect_uri, $expires);
        $this->redbean_storage->createAuthCode('fake', $client, $data, $redirect_uri, $expires);

        $results = $this->redbean->findAll($this->tables['code'], 'code = ?', [$code]);
        $this->assertCount(1, $results);

        $code_bean = reset($results);
        $this->assertEquals($code_bean->client_id, $client->id);
        $this->assertEquals($code_bean->redirect_uri, $redirect_uri);
        $this->assertEquals($code_bean->expires_in, $expires);
        $this->assertEquals($code_bean->has_expired, 0);
        $this->assertEquals($code_bean->token, null);
    }

    public function testGetAuthCode() {
        $client_bean = $this->redbean->dispense($this->tables['code']);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $code_str = md5(uniqid(time()));
        $data = [];
        $redirect_uri = 'http://google.com';
        $expires = time() + 3600;
        $this->redbean_storage->createAuthCode($code_str, $client, $data, $redirect_uri, $expires);

        $code = $this->redbean_storage->getAuthCode($code_str);
        $this->assertInstanceOf('\ebussola\oauth\Code', $code);
        $this->assertEquals($code->client_id, $client->id);
        $this->assertEquals($code->redirect_uri, $redirect_uri);
        $this->assertEquals($code->expires_in, $expires);
        $this->assertEquals($code->has_expired, 0);
        $this->assertEquals($code->token, null);
    }

    public function testMarkAuthCodeAsUsed() {
        $client_bean = $this->redbean->dispense($this->tables['code']);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $code_str = md5(uniqid(time()));
        $data = [];
        $redirect_uri = 'http://google.com';
        $expires = time() + 3600;
        $this->redbean_storage->createAuthCode($code_str, $client, $data, $redirect_uri, $expires);

        $this->redbean_storage->markAuthCodeAsUsed($code_str);

        $results = $this->redbean->findAll($this->tables['code'], 'code = ?', [$code_str]);
        $this->assertCount(0, $results);
    }

}