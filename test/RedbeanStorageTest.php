<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 19/03/14
 * Time: 12:21
 */

class RedbeanStorageTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \ebussola\oauth\RedbeanStorage
     */
    private $redbean_storage;

    /**
     * @var RedBean_Facade
     */
    private $redbean;

    public function setUp() {
        $this->redbean = new RedBean_Facade();
        $this->redbean->setup('sqlite::memory:');

        $this->redbean_storage = new \ebussola\oauth\RedbeanStorage($this->redbean);
    }

    public function testGetClient() {
        $client_bean = $this->redbean->dispense(\ebussola\oauth\RedbeanStorage::TABLE_CLIENTS);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $client->id = 1;
        $client->redirect_uris = array('localhost');
        $client->client_secret = '823984y9ncy9ny4hh284c823';
        $this->redbean->store($client->getBean());

        $client = $this->redbean_storage->getClient(1);
        $this->assertInstanceOf('\ebussola\oauth\Client', $client);
        $this->assertEquals(array('localhost'), $client->redirect_uris);
    }

    public function testCheckClientCredentials() {
        $client_bean = $this->redbean->dispense(\ebussola\oauth\RedbeanStorage::TABLE_CLIENTS);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $client->redirect_uris = array('localhost');
        $client->client_secret = 'shhhh_this_is_secret';

        $this->assertFalse($this->redbean_storage->checkClientCredentials($client, 'something'));
        $this->assertTrue($this->redbean_storage->checkClientCredentials($client, 'shhhh_this_is_secret'));
    }

    public function testGetAccessToken() {
        $access_token_bean = $this->redbean->dispense(\ebussola\oauth\RedbeanStorage::TABLE_ACCESS_TOKENS);
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
    }

    public function testCreateAccessToken() {
        $client_bean = $this->redbean->dispense(\ebussola\oauth\RedbeanStorage::TABLE_CLIENTS);

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

}