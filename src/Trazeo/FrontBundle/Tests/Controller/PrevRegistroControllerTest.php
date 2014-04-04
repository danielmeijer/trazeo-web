<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrevRegistroControllerTest extends WebTestCase
{
    public function testPrevRegistro()
    {
    	$client = static::createClient();
    	
    	$crawler = $client->request('GET', '/');
    	$form = $crawler->selectButton('submitPrevRegister')->form();
    	$crawler = $client->submit($form, array(
    			'email' => 'syamuza@sopinet.com',
    	
    	));    	
    }    
}
