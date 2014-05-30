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
    			'fos_user_registration_form[email]' => 'syamuza@sopinet.com',
    			'fos_user_registration_form[username]' => 'Sergio',
    			'fos_user_registration_form[plainPassword][first]' => 'password',
    			'fos_user_registration_form[plainPassword][second]' => 'password',
    	
    	));    	
    }
}