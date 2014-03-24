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
        $crawler = $client->submit($form, array('_username' => 'trazeo', '_password' => 'trazeo'));
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        
        $this->assertGreaterThan(
        		0, 
        		$crawler->filter('html:contains("trazeo_niño_1")')->count()
        );
    }
}

