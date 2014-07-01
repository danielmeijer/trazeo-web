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
        
        //$attr = array('icon'=>'fa-home fa-2x','attributes'=>array('rel'=>'tooltip-right','data-original-title'=>'Inicio'));
        
        // Home
        $menu->addChild('Home', array(
        	'route' => 'panel_dashboard',
        ))
                
        ->setLabel("");
        $menu['Home']->setAttribute('icon', 'fa-home fa-2x');
        $menu['Home']->setAttribute('tooltip', 'Menu.tooltip.home');
        if ($options['activeMenu'] == "home") {
        	$menu['Home']->setAttribute('class', 'active');
        }

        // Children
        $menu->addChild('Child', array(
        		'route' => 'panel_child'
        ))
        ->setLabel("");
        $menu['Child']->setAttribute('icon', 'fa-user fa-2x');
        $menu['Child']->setAttribute('tooltip', 'Menu.tooltip.child');
        if ($options['activeMenu'] == "route") {
        	$menu['Child']->setAttribute('class', 'active');
        }
        
        // Groups
        $menu->addChild('Group', array(
        		'route' => 'panel_group'
        		))
        ->setLabel("");
        $menu['Group']->setAttribute('icon', 'fa-users fa-2x');
        $menu['Group']->setAttribute('tooltip', 'Menu.tooltip.group');
        if ($options['activeMenu'] == "group") {
        	$menu['Group']->setAttribute('class', 'active');
        }
 
        // Routes
        $menu->addChild('Route', array(
        		'route' => 'panel_route'
        ))
        ->setLabel("");
        $menu['Route']->setAttribute('icon', 'fa-compass fa-2x');
        $menu['Route']->setAttribute('tooltip', 'Menu.tooltip.route');
        if ($options['activeMenu'] == "route") {
        	$menu['Route']->setAttribute('class', 'active');
        }

        // Points
        $menu->addChild('Point', array(
                'route' => 'panel_point'
        ))
        ->setLabel("");
        $menu['Point']->setAttribute('icon', 'fa-trophy fa-2x');
        $menu['Point']->setAttribute('tooltip', 'Menu.tooltip.point');
        if ($options['activeMenu'] == "point") {
            $menu['Point']->setAttribute('class', 'active');
        }              

        return $menu;
    }
}