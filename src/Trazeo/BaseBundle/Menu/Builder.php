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

        // Children
        $menu->addChild('Child', array(
        		'route' => 'panel_child'
        ))
        ->setLabel($this->container->get('translator')->trans('Menu.child'));
        $menu['Child']->setAttribute('icon', 'fa-user');
        if ($options['activeMenu'] == "routes") {
        	$menu['Child']->setAttribute('class', 'active');
        }
        
        // Groups
        $menu->addChild('Group', array(
        		'route' => 'panel_group'
        		))
        ->setLabel($this->container->get('translator')->trans('Menu.group'));
        $menu['Group']->setAttribute('icon', 'fa-users');
        if ($options['activeMenu'] == "group") {
        	$menu['Group']->setAttribute('class', 'active');
        }
        
        // Routes
        $menu->addChild('Route', array(
        		'route' => 'panel_route'
        ))
        ->setLabel($this->container->get('translator')->trans('Menu.route'));
        $menu['Route']->setAttribute('icon', 'fa-location-arrow');
        if ($options['activeMenu'] == "route") {
        	$menu['Route']->setAttribute('class', 'active');
        }
        
        

        return $menu;
    }
}