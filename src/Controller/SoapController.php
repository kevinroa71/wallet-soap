<?php

namespace App\Controller;

use App\Service\ClienteService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SoapController
{
    protected $service;
    protected $container;

    public function __construct(ClienteService $service, ContainerInterface $container)
    {
        $this->service = $service;
        $this->container = $container;
    }

    /**
     * @Route("/soap")
     */
    public function __invoke()
    {
        $wsdl = $this->container->getParameter('app.soap.wsdl.dir').'/cliente.wsdl';
        $soapServer = new \SoapServer($wsdl);
        $soapServer->setObject($this->service);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }
}
