<?php
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class PanelGroupsControllerText extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/trazeo-web/web/app_dev.php/login");
    $this->start();
    sleep(1);
    $this->open("/trazeo-web/web/app_dev.php/login");
    $this->type("id=username", "aarrabal@sopinet.com");
    $this->type("id=password", "ee");
    $this->click("name=submitLogin");
    $this->waitForPageToLoad("30000"); 
    $this->db->exec("BEGIN"); 
  }

  public function testCityFilter()
  {        
    $this->click("//div[@id='groups']/div[10]");
    $this->click("//div[@id='wrapper']/nav[2]/div/ul/li[3]/a/i");
    $this->waitForPageToLoad("30000");
    $before = $this->getXpathCount("//div[@id='groups']/div[not(contains(@style,'display: none'))]");
    $this->select("name=city", "label=Zuheros");
    $this->click("css=option[value=\"Zuheros\"]");
    //comprobamos que filtran
    $this-> assertGreaterThan($before,
      $this->getXpathCount("//div[@id='groups']/div[not(contains(@style,'display: none'))]")  
    );   
  }

  public function testCreateGroup()
  {
    $this->click("//div[@id='wrapper']/nav[2]/div/ul/li[3]/a/i");
    $this->waitForPageToLoad("30000");
    $this->click("id=new_group");
    $this->waitForPageToLoad("30000");
    $this->type("id=trazeo_basebundle_group_name", "Test_Case_Group");
    $this->click("//button[@type='submit']");
    $this->click("id=later");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Test_Case_Group", $this->getText("css=h3.alert > b:nth-child(2)"));
  }

  public function testSuggestion()
  {
    $this->assertTrue($this->isTextPresent("Este grupo no tiene ruta definida aun, haz click aquí para crear la ruta."));
    $this->click("link=Crear nueva ruta");
    $this->waitForPageToLoad("30000");
    $this->type("id=trazeo_basebundle_route_name", "Test_Case_Route");
    $this->click("//button[@type='submit']");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='wrapper']/nav[2]/div/ul/li/a/i");
    $this->waitForPageToLoad("30000");
    $this->assertFalse($this->isTextPresent("Este grupo no tiene ruta definida aun, haz click aquí para crear la ruta."));
  }

  public function testDeleteGroup()
  {
    $this->open("http://localhost/trazeo-web/web/app_dev.php/panel/");
    $this->click("xpath=(//a[contains(text(),'Eliminar')])[7]");
    $this->click("link=Sí");
    $this->waitForPageToLoad("30000");
    $this->assertFalse($this->isTextPresent("Test_Case_Group"));
  }  
  function tearDown() {
     $this->db->exec("ROLLBACK");
     parent::tearDown();
   }
}
?>