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
        $menu->addChild('Children', array(
        		'route' => 'panel_children'
        ))
        ->setLabel($this->container->get('translator')->trans('Menu.children'));
        $menu['Children']->setAttribute('icon', 'fa-user');
        if ($options['activeMenu'] == "routes") {
        	$menu['Children']->setAttribute('class', 'active');
        }
        
        // Groups
        $menu->addChild('Groups', array(
        		'route' => 'panel_groups'
        		))
        ->setLabel($this->container->get('translator')->trans('Menu.groups'));
        $menu['Groups']->setAttribute('icon', 'fa-users');
        if ($options['activeMenu'] == "groups") {
        	$menu['Groups']->setAttribute('class', 'active');
        }
        
        // Routes
        $menu->addChild('Routes', array(
        		'route' => 'panel_routes'
        ))
        ->setLabel($this->container->get('translator')->trans('Menu.routes'));
        $menu['Routes']->setAttribute('icon', 'fa-location-arrow');
        if ($options['activeMenu'] == "routes") {
        	$menu['Routes']->setAttribute('class', 'active');
        }
        
        

        return $menu;
    }
}