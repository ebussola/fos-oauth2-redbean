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
        $this->assertInstanceOf('\ebussola\oauth\client\Client', $client);
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

}