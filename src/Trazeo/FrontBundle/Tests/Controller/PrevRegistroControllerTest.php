<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrevRegistroControllerTest extends WebTestCase
{
    public function testPrevRegistro()
    {
    	$client = static::createClient();
    	
    	$crawler = $client->request('GET', '/');
    	$link = $crawler->selectLink('Accede a Trazeo')->link();
    	$crawler = $client->click($link);
    	
    }    
}
