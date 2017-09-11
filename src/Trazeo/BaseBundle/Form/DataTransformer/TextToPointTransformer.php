<?php
namespace Trazeo\BaseBundle\Form\DataTransformer;

use FOS\UserBundle\Model\UserManager;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;

class TextToPointTransformer implements DataTransformerInterface
{
    /**
    public function __construct(Container $container, $account)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        $this->account = $account;
    }
    **/

    public function transform($simplePoint)
    {
        if ($simplePoint != null) {
            /** @var SimplePoint $simplePoint */
            return $simplePoint->__toString();
        }
    }


    public function reverseTransform($text)
    {
        if ($text != null) {
            $temp = str_replace("POINT(", "", $text);
            $temp = str_replace(")", "", $temp);
            $arrayTemp = explode(" ", $temp);
            $coor1 = trim($arrayTemp[0]);
            $coor2 = trim($arrayTemp[1]);

            return new SimplePoint($coor1, $coor2);
        }
    }
}