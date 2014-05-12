<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sopinet\UserPreferencesBundle\Entity\UserSetting;

class LoadSettingData extends AbstractFixture implements OrderedFixtureInterface
{	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$setting_email = new UserSetting();
		$setting_email->setType("Enum");
		$setting_email->setName("notification_email");
		$setting_email->setIcon("icon-comments-alt");
		$setting_email->setOptions("never,now");
		$setting_email->setDefaultOption("now");
		
		$manager->persist($setting_email);
		$manager->flush();
	}
	
	public function getOrder(){
		return 1;
	}
}