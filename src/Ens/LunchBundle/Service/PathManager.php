<?php
/**
 * Created by PhpStorm.
 * User: d.baryshev
 * Date: 10.06.2015
 * Time: 12:55
 */

namespace Ens\LunchBundle\Service;


use Symfony\Component\DependencyInjection\Container;

class PathManager {

    private $container;
    private $webPath;

    function __construct(Container $container)
    {
        $this->container = $container;
        $this->container->getParameter('kernel.root_dir')
        $this->webPath = $this->get('kernel')->getRootDir() . '/../web' ;
    }

    public function getOrderPath()
    {
        $webPath = $this->get('kernel')->getRootDir() . '/../web' ;
        $this->pathDocuments = $path['documents'];
        $this->pathOrders = $path['orders'];

    }

    public function getDocumentPath()
    {

    }
}