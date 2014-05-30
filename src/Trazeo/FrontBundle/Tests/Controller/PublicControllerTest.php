<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        //Comprueba que se carga el contenido de la página
        $this->assertGreaterThan(
        		0,
        		$crawler->filter('div.videowrapper')->count()
        );
        
        $link = $crawler->filter("a:contains('Accede a Trazeo')")->link();
        $crawler = $client->click($link);

        
        $form = $crawler->selectButton('submitLogin')->form();
        $client->submit($form, array('_username' => 'trazeo', '_password' => 'trazeo'));
        $this->assertTrue(
        		$client->getResponse()->isRedirect('/panel')
        );        

    }
    
    public function testCofinanciadores()
    {
    	$client = static::createClient();
    
    	$crawler = $client->request('GET', '/cofinanciadores');
    
    	$this->assertGreaterThan(
    		0,
    		$crawler->filter('div.cofinanciadores')->count()
    	);
    }
}
