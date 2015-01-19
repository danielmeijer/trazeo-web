<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
    	$client = static::createClient();
    	
    	$crawler = $client->request('GET', '/register/');
    	$form = $crawler->selectButton('Registrar')->form();
    	$crawler = $client->submit($form, array(
    			'trazeo_user_registration_form[email][first]' => 'syamuza@sopinet.com',
    			'trazeo_user_registration_form[email][second]' => 'syamuza@sopinet.com',
    			'trazeo_user_registration_form[plainPassword][first]' => 'password',
    			'trazeo_user_registration_form[plainPassword][second]' => 'password',
    	
    	));    	
    }
}