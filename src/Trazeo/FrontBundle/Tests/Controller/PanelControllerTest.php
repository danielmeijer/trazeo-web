<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PanelControllerTest extends WebTestCase
{
    public function testIndex()
    {        
        $client = static::createClient();        

        // Login - Demo
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('submitLogin')->form();
        $crawler = $client->submit($form, array('_username' => 'aarrabal@sopinet.com', '_password' => 'ee'));
        // aserting if user was loged sucessfully
       	$this->assertTrue(
        		$client->getResponse()->isRedirect('/panel')
        );
    }
}

