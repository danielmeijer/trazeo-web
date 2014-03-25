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
        
        $buttonCrawlerNode = $crawler->selectButton('submit');
        
       	//Se ha registrado correctamente
        $form = $buttonCrawlerNode->form(array(
        		'email' => 'check@sopinet.es',
        ));
        
        $ok = $client->submit($form);
        
        $crawler = $client->followRedirect();
        
        $this->assertGreaterThan(
        		0,
        		$crawler->filter('html:contains("correctamente")')->count()
        );
        
        $form = $buttonCrawlerNode->form(array(
        		'email' => 'check@sopinet.es'
        		), 'DELETE');
        $client->submit($form);
        
        //Usuario ya registrado
        $form = $buttonCrawlerNode->form(array(
        		'email' => 'lumilo8@gmail.com',
        ));
        
        $client->submit($form);
        $crawler = $client->followRedirect();
        
        $this->assertGreaterThan(
        		0,
        		$crawler->filter('html:contains("existe")')->count()
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
