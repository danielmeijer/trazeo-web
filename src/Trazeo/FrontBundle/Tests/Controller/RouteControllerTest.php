<?php

namespace Trazeo\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\CssSelector\CssSelector;

class RouteControllerTest extends WebTestCase
{
 	function setUp() {
 		$this->db->exec("BEGIN");
 	 }
 	function tearDown() { $this->db->exec("ROLLBACK"); }
	
    public function testCreate()
    {
    	$client = static::createClient();
    	
    	//Parte del login del cliente    	   
    	$crawler = $client->request('GET', '/login');
    	$form = $crawler->selectButton('submitLogin')->form();
    	$client->submit($form, array('_username' => 'aarrabal@sopinet.com', '_password' => '10u2M10u2P'));
    	$client->insulate();//Es necesario para manejar las dependencías externas
    	$this->assertTrue(
    			$client->getResponse()->isRedirect('/panel')
    	);    	
    	
    	//Creamos una nueva ruta
    	$crawler = $client->request('GET', '/panel/route/new');
    	$form=$crawler->selectButton('Guardar')->form();
    	$client->followRedirects(true);//Una vez enviado el formulario comprobaremos si se ha creado correctamente la ruta 
    	$crawler = $client->submit($form, array(
    			'trazeo_basebundle_route[name]' => 'Route_Controller_Test',
    			'trazeo_basebundle_route[description]' => 'Esta es la descipción',	
    			'trazeo_basebundle_route[country]' => $form['trazeo_basebundle_route[country]']->select('340')
    	));

    	//comprobamos que se crea correctamente
    	$this->	assertGreaterThan(0,
    			$crawler->filter('html:contains("Route_Controller_Test")')->count()
    	);    	 
    	
    }
   
    /**
     * @depends testCreate
     */
    public function testDelete(){
    	$client = static::createClient();
    	
    	//Parte del login del cliente
    	$crawler = $client->request('GET', '/login');
    	$form = $crawler->selectButton('submitLogin')->form();
    	$client->submit($form, array('_username' => 'aarrabal@sopinet.com', '_password' => '10u2M10u2P'));
    	$client->insulate();//Es necesario para manejar las dependencías externas
    	$this->assertTrue(
    			$client->getResponse()->isRedirect('/panel')
    	);
    	//Creamos una nueva ruta
    	$client->followRedirects(true);
    	$crawler = $client->request('GET', '/panel/route');
    	$nodes=$crawler->selectLink('Eliminar');
    	$link=$crawler->selectLink('Eliminar')->reduce(function ($nodes, $i) {
    		$node=$nodes->eq($i);
    		if($i==0)var_dump($node->parents()->html());
        	return false;//$node->parents();
    	});;
    }
}