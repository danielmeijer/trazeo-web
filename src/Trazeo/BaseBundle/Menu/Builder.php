<?php
namespace Trazeo\BaseBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function sidebarMenu(FactoryInterface $factory, array $options)
    {   	
        $menu = $factory->createItem('root');

        $menu->setChildrenAttributes(array('class' => 'nav navbar-nav'));
        if (!isset($options['activeMenu'])) $options['activeMenu'] = "Home";
        
        //$attr = array('icon'=>'fa-home fa-2x','attributes'=>array('rel'=>'tooltip-right','data-original-title'=>'Inicio'));
        
        // Home
        $menu->addChild('Home', array(
        	'route' => 'panel_dashboard',
        ))
                
        ->setLabel("");
        $menu['Home']->setAttribute('icon', 'fa-home fa-2x');
        $menu['Home']->setAttribute('tooltip', 'Menu.tooltip.home');
        $menu['Home']->setAttribute('id', 'home');
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
        if ($options['activeMenu'] == "Child") {
        	$menu['Child']->setAttribute('class', 'active');
        }
        
        // Groups
        $menu->addChild('Group', array(
        		'route' => 'panel_group'
        		))
        ->setLabel("");
        $menu['Group']->setAttribute('icon', 'fa-users fa-2x');
        $menu['Group']->setAttribute('tooltip', 'Menu.tooltip.group');
        if ($options['activeMenu'] == "Group") {
        	$menu['Group']->setAttribute('class', 'active');
        }
 
        // Points
        $menu->addChild('Point', array(
                'route' => 'panel_point'
        ))
        ->setLabel("");
        $menu['Point']->setAttribute('icon', 'fa-trophy fa-2x');
        $menu['Point']->setAttribute('tooltip', 'Menu.tooltip.point');
        if ($options['activeMenu'] == "Point") {
            $menu['Point']->setAttribute('class', 'active');
        }              

        if($this->container->get('security.context')->isGranted('ROLE_CATALOG')){
            // Points
            $menu->addChild('Catalog', array(
                'route' => 'panel_catalogitems_list'
            ))
                ->setLabel("");
            $menu['Catalog']->setAttribute('icon', 'fa-file fa-2x');
            $menu['Catalog']->setAttribute('tooltip', 'Menu.tooltip.catalog');
            if ($options['activeMenu'] == "Catalog") {
                $menu['Catalog']->setAttribute('class', 'active');
            }
        }
        return $menu;
    }
}