<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
    	$client = static::createClient();
    	$crawler = $client->request('GET', '/register');
   
    	$form = $crawler->selectButton('button[type=submit]')->form();
    	$form['fos_user_registration_form_email'] = 'syamuza@sopinet.com';
    	$form['fos_user_registration_form_username'] = 'Sergio';
    	$form['fos_user_registration_form_plainPassword_first'] = 'password';
    	$form['fos_user_registration_form_plainPassword_second'] = 'password';	
    	$crawler = $client->submit($form);

    }
}