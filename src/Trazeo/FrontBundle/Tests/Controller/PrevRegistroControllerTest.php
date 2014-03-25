<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrevRegistroControllerTest extends WebTestCase
{
    public function testPrevRegistro()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/prev_registro');

//         $buttonCrawlerNode = $crawler->selectButton('submit');
        
//         $form = $buttonCrawlerNode->form();
        
//         $form = $buttonCrawlerNode->form(array(
//         		'email' => 'lumilo8@gmail.com'
//         ));
        
//         $client->submit($form);
    
    }
    
    
}
