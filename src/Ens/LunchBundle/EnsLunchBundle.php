<?php

namespace Ens\LunchBundle;

use Ens\LunchBundle\DependencyInjection\Compiler\OverrideUserManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnsLunchBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder){
        parent::build($containerBuilder);

        $containerBuilder->addCompilerPass(new OverrideUserManager());
    }
}
