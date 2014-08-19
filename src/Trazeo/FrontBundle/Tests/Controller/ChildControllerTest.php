<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
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
  }

  public function testCreateChild()
  {
    $this->open("http://localhost/trazeo-web/web/app_dev.php/panel/");
    $this->click("css=#childs > div:nth-child(1) > div.pull-right > a:nth-child(1)");
    $this->waitForPageToLoad("30000");
    $this->type("id=trazeo_basebundle_child_nick", "Test_Case_Child");
    $this->type("id=trazeo_basebundle_child_date_birth", "02/10/2008");
    $this->click("//button[@type='submit']");
    $this->waitForPageToLoad("30000");
    $this->assertContains("Test_Case_Child", $this->getText("css=#childs"));
  }

  public function testSuggestionChild()
  {
    $this->open("http://localhost/trazeo-web/web/app_dev.php/panel/");
    $this->click("//div[@id='wrapper']/nav[2]/div/ul/li[2]/a/i");
    $this->waitForPageToLoad("30000");
    $this->assertEquals('en el perfil de tu hijo/a puede rellenar el “Centro Educativo” al que pertenece', $this->getText("css=div.trip-content"));
    $this->click("xpath=(//a[@type='button'])[3]");
    $this->waitForPageToLoad("30000");
    $this->click("id=trazeo_basebundle_child_scholl");
    $this->type("id=trazeo_basebundle_child_scholl", "Test_Case_School");
    $this->click("//button[@type='submit']");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='wrapper']/nav[2]/div/ul/li[2]/a");
    $this->waitForPageToLoad("30000");
    $this->assertFalse($this->isElementPresent("css=div.trip-content"));
  }

  public function testDeleteChild()
  {
    $this->open("http://localhost/trazeo-web/web/app_dev.php/panel/");
    $this->click("css=#childs > div:nth-child(3) > div.col-md-2 > a:nth-child(2)");
    $this->click("link=Sí");
    $this->waitForPageToLoad("30000");
  }

}
?>