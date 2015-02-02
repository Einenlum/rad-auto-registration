<?php

namespace Knp\Rad\AutoRegistration\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DoctrineObjectRepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->has('doctrine')) {
            return;
        }

        $configuration = $container->getParameter('knp_rad_auto_registration.configuration');

        if (false === $configuration['doctrine']) {
            return;
        }

        $generator     = $container->get($configuration['service_name_generator']);
        $doctrine      = $container->get('doctrine');
        $managers      = $doctrine->getManagers();
        $metadata      = [];

        foreach ($managers as $name => $manager) {
            $metadata = array_merge($metadata, $manager->getMetadataFactory()->getAllMetadata());
        }

        foreach ($metadata as $entity) {
            $classname  = $entity->getName();
            $repository = sprintf('%sRepository', $classname);
            $service    = $generator->generateFromClassname($repository);
            $definition = (new Definition('Doctrine\Common\Persistence\ObjectRepository'))
                ->setFactoryService('doctrine')
                ->setFactoryMethod('getRepository')
                ->addArgument($classname)
            ;

            $container->setDefinition($service, $definition);
        }
    }
}
