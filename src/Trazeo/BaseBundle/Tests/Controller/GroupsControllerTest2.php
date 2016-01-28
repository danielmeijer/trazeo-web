?php

require_once 'Testing/Selenium.php';

class Example extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this = new Testing_Selenium("*chrome", "http://localhost/epp2/web/app_dev.php/admin/test/base/signup/create")
    $this->open("/trazeo-web/web/app_dev.php/login");
    $this->type("id=username", "aarrabal@sopinet.com");
    $this->type("id=password", "ee");
    $this->click("name=submitLogin");
    $this->click("//div[@id='groups']/div[10]");
    $this->click("//div[@id='wrapper']/nav[2]/div/ul/li[3]/a/i");
    $this->waitForPageToLoad("30000");
    $this->select("name=city", "label=Zuheros");
    $this->click("css=option[value=\"Zuheros\"]");
  }
}
?>