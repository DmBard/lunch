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

    function __construct(Container $container, $root)
    {
        $this->container = $container;
        $this->webPath = $root. '/../web/uploads';
    }

    public function getOrderPath()
    {
        $result = $this->getPath('orders');
        return $result;
    }

    public function getDocumentPath()
    {
        $result = $this->getPath('documents');
        return $result;
    }

    /**
     * @return string
     */
    public function getPath($name)
    {
        $rootPath = $this->webPath;
        $docPath = $this->container->getParameter('upload_path')[$name];
        $result = sprintf('%s%s', $rootPath, $docPath);

        return $result;
    }
}