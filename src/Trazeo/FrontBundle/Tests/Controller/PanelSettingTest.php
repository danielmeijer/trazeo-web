<?php
class PanelSettingTest extends PHPUnit_Extensions_SeleniumTestCase
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

  public function testSettings()
  {
    $this->open("http://localhost/trazeo-web/web/app_dev.php/panel/");
    $this->click("//div[@id='wrapper']/nav/ul/li[2]/a");
    $this->click("link=Configuración");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("Notificaciones por email"));
    $this->assertTrue($this->isTextPresent("Connect with CiviClub"));
  }
}
?>