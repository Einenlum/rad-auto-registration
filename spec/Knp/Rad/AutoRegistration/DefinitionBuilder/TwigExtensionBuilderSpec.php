<?php

namespace spec\Knp\Rad\AutoRegistration\DefinitionBuilder;

use Knp\Rad\AutoRegistration\Finder\BundleFinder;
use Knp\Rad\AutoRegistration\Kernel\KernelWrapper;
use Knp\Rad\AutoRegistration\Reflection\ClassAnalyzer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TwigExtensionBuilderSpec extends ObjectBehavior
{
    function let(KernelWrapper $kernel, BundleFinder $finder, ClassAnalyzer $analyzer)
    {
        $this->beConstructedWith($kernel, $finder, $analyzer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtensionBuilder');
    }

    function it_create_definitions_from_constructable_twig_extensions($finder, $analyzer)
    {
        $finder->findClasses(Argument::cetera())->willReturn([
            'spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension1',
            'spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension2',
            'spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension3',
        ]);

        $analyzer->canBeConstructed('spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension1')->willReturn(true);
        $analyzer->canBeConstructed('spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension2')->willReturn(false);
        $analyzer->canBeConstructed('spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension3')->willReturn(true);

        $definitions = $this->buildDefinitions(['public' => false]);

        $definition = $definitions['spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension1'];
        $definition->shouldHaveType('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldReturn('spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension1');
        $definition->isPublic()->shouldReturn(false);
        $definition->hasTag('twig.extension')->shouldReturn(true);

        $definition = $definitions['spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension3'];
        $definition->shouldHaveType('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldReturn('spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension3');
        $definition->isPublic()->shouldReturn(false);
        $definition->hasTag('twig.extension')->shouldReturn(true);

        expect(array_key_exists('spec\Knp\Rad\AutoRegistration\DefinitionBuilder\TwigExtension2', $definition))->toBe(false);
    }
}

class TwigExtension1
{
}

class TwigExtension2
{
}

class TwigExtension3
{
}
