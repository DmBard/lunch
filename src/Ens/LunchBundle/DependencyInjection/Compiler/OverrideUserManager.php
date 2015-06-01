<?php
/**
 * Created by PhpStorm.
 * User: d.baryshev
 * Date: 01.06.2015
 * Time: 15:41
 */

namespace Ens\LunchBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideUserManager implements CompilerPassInterface {
    public function process(ContainerBuilder $builder){
        $def = $builder->getDefinition('fos_user.user_manager.default');
        $def->setClass('Ens\LunchBundle\Security\UserManager');
    }
}