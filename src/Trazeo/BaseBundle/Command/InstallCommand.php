<?php 
namespace Trazeo\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

class InstallCommand extends ContainerAwareCommand
{
	# php app/console trazeo:install
	
    protected function configure()
    {
        $this
            ->setName('trazeo:install')
            ->setDescription('Install basic configuration for trazeo')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$output->writeln("<info>Install</info>");
    	$con = $this->getContainer();
    	
    	$rootBase = $con->get('kernel')->getRootDir().'/../web/bundles/sopinetbootstrapextend/export';
    	$rootLink = $con->get('kernel')->getRootDir().'/../web/include';
    	$base1 = $rootBase . "/font";
    	$base2 = $rootBase . "/images";
    	$base3 = $rootBase . "/img";
    	$link1 = $rootLink . "/font";
    	$link2 = $rootLink . "/images";
    	$link3 = $rootLink . "/img";
    	symlink($base1, $link1);
    	symlink($base2, $link2);
    	symlink($base3, $link3);
    	//TODO: Si symlink falla, borrar la ruta y volver a generar.
    	
    	$output->writeln("<info>Finish</info>");
    }
}
