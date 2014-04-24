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
        
        $attr = array('icon'=>'fa-home fa-2x','attributes'=>array('rel'=>'tooltip-right','data-original-title'=>'Inicio'));
        
        // Home
        $menu->addChild('Home', array(
        	'route' => 'panel_dashboard',
        ))
                
        ->setLabel("");
        $menu['Home']->setAttributes($attr);
        if ($options['activeMenu'] == "home") {
        	$menu['Home']->setAttribute('class', 'active');
        }

        // Children
        $menu->addChild('Child', array(
        		'route' => 'panel_child'
        ))
        ->setLabel("");
        $menu['Child']->setAttribute('icon', 'fa-user fa-2x');
        if ($options['activeMenu'] == "routes") {
        	$menu['Child']->setAttribute('class', 'active');
        }
        
        // Groups
        $menu->addChild('Group', array(
        		'route' => 'panel_group'
        		))
        ->setLabel("");
        $menu['Group']->setAttribute('icon', 'fa-users fa-2x');
        if ($options['activeMenu'] == "group") {
        	$menu['Group']->setAttribute('class', 'active');
        }
        
        // Routes
        $menu->addChild('Route', array(
        		'route' => 'panel_route'
        ))
        ->setLabel("");
        $menu['Route']->setAttribute('icon', 'fa-location-arrow fa-2x');
        if ($options['activeMenu'] == "route") {
        	$menu['Route']->setAttribute('class', 'active');
        }
        
        

        return $menu;
    }
}