<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Hip\MandrillBundle\Message;
use Symfony\Component\Form\Form;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Service\Helper;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;
use Trazeo\MyPageBundle\Form\FormContactType;
use Symfony\Component\HttpFoundation\Request;

class GeneralData extends ModuleAbstract
{
    function prepareFront($container, Module $module = null)
    {
        $groups = $module->getMenu()->getPage()->getGroups();

        /** @var ERideRepository $repositoryERide */
        $repositoryERide = $container->getDoctrine()->getRepository('TrazeoBaseBundle:ERide');
        /** @var QueryBuilder $qb */
        $qb = $repositoryERide->createQueryBuilder("r");

        // Filtro por Grupos
        $group_ids = array();
        foreach($groups as $g) {
            $group_ids[] = $g->getId();
        }
        $qb->where('r.groupid IN (:group_ids)');
        $qb->setParameter('group_ids', $group_ids);

        $rides = $qb->getQuery()->getResult();

        /** @var Helper $helper */
        $helper = $container->get('trazeo_base_helper');

        $data = $helper->getDataFromRides($rides);

        /**
         * Data return

            [participaciones]: int 4050
            [paseos]: int 469
            [km]: float 4975.1
            [co2]: float 1990.04
            [litros_combustible]: float 398.01
            [euros_combustible]: float 557.214
            [tiempo_formato]

         **/
        return $data;
    }

    public function getAdminDescription(Module $module) {
        $description = "Este módulo insertará los datos globales de su proyecto en su página personalizada. Podrá indicar qué datos desea mostrar.";
        return $description;
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        $builder->add('contentArray', 'choice', array(
            'choices' => array(
                'participaciones'   => 'Participaciones',
                'paseos' => 'Paseos',
                'km'   => 'Kilómetros',
                'co2'   => 'CO2',
                'litros_combustible'   => 'Litros de Combustible',
                'euros_combustible'   => 'Euros de Combustible',
                'tiempo_formato'   => 'Tiempo caminado',
            ),
            'multiple' => true,
            'label' => "¿Qué datos desea mostrar?"
        ));
        return $builder;
    }
}