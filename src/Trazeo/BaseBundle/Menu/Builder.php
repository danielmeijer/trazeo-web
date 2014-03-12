<?php
namespace Trazeo\BaseBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function sidebarMenu(FactoryInterface $factory, array $options)
    {   	
        $menu = $factory->createItem('root');

        if (!isset($options['activeMenu'])) $options['activeMenu'] = "Home";
        
        // Home
        $menu->addChild('Home', array(
        	'route' => 'panel_dashboard'
        ))
        ->setLabel($this->container->get('translator')->trans('Menu.home'));
        $menu['Home']->setAttribute('icon', 'fa-home');
        if ($options['activeMenu'] == "home") {
        	$menu['Home']->setAttribute('class', 'active');
        }

        // Timeline
        $menu->addChild('Timeline', array(
        		'route' => 'panel_dashboard'
        		))
        ->setLabel($this->container->get('translator')->trans('Menu.timeline'));
        $menu['Timeline']->setAttribute('icon', 'fa-plus');
        if ($options['activeMenu'] == "new") {
        	$menu['Timeline']->setAttribute('class', 'active');
        }
        
        // Rutes
        $menu->addChild('Rutes', array(
        		'route' => 'panel_dashboard'
        ))
        ->setLabel($this->container->get('translator')->trans('Menu.rutes'));
        $menu['Rutes']->setAttribute('icon', 'fa-user')
        ->addChild('Timeline', array(
        		'route' => 'panel_dashboard'
        		));
        if ($options['activeMenu'] == "list") {
        	$menu['Rutes']->setAttribute('class', 'active');
        }

        return $menu;
    }
}