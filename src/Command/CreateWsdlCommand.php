<?php

namespace App\Command;

use PHP2WSDL\PHPClass2WSDL;
use App\Service\ClienteService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateWsdlCommand extends Command
{
    protected static $defaultName = 'app:create-wsdl';
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Creates files wsdl for services soap.')
            ->setHelp('This command allows you to create all files wsdl for services soap.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceURI = $this->container->getParameter('app.soap.host').'/index.php/soap';
        $pathFiles  = $this->container->getParameter('app.soap.wsdl.dir');

        $wsdlGenerator = new PHPClass2WSDL(ClienteService::class, $serviceURI);
        $wsdlGenerator->generateWSDL(true);
        $path = $pathFiles.'/cliente.wsdl';
        $wsdlGenerator->save($path);

        return Command::SUCCESS;
    }
}
