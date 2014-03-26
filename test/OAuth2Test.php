<?php
/**
 * Created by PhpStorm.
 * User: Leonardo Shinagawa
 * Date: 19/03/14
 * Time: 16:38
 */

class OAuth2Test extends PHPUnit_Framework_TestCase {

    /**
     * @var \OAuth2\OAuth2
     */
    private $oauth2;

    /**
     * @var GrantCode
     */
    private $storage;

    public function setUp() {
        $redbean = new RedBean_Facade();
        $redbean->setup('sqlite::memory:');
        $tables = array(
            'client'       => 'clients',
            'access_token' => 'accesstokens',
            'code'         => 'codes'
        );
        $this->storage = new GrantCode($redbean, $tables);

        $client_bean = $redbean->dispense($tables['client']);
        $client = new \ebussola\oauth\client\Client($client_bean);
        $client->redirect_uris = array();
        $client->client_secret = 'xpto';
        $redbean->store($client->getBean());

        $this->oauth2 = new \OAuth2\OAuth2($this->storage);
    }

    public function testUse() {
        $client = $this->storage->getClient(1);
        $access_token = $this->oauth2->createAccessToken($client, array());
        $a = $this->oauth2->verifyAccessToken($access_token['access_token']);

        $this->assertInstanceOf('\ebussola\oauth\AccessToken', $a);
    }

}