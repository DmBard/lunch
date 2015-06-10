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
        $this->webPath = realpath('%kernel.root_dir%'). '/web/uploads';
    }

    public function getOrderPath()
    {
        $rootPath = $this->webPath;
        $orderPath = $this->container->getParameter('upload_path')['orders'];
        $result = sprintf('%s%s', $rootPath, $orderPath);
        return $result;
    }

    public function getDocumentPath()
    {
        $rootPath = $this->webPath;
        $docPath = $this->container->getParameter('upload_path')['documents'];
        $result = sprintf('%s%s', $rootPath, $docPath);
        return $result;
    }
}