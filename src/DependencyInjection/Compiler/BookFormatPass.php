<?php

namespace App\DependencyInjection\Compiler;

use App\Service\BookParser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BookFormatPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(BookParser::class)) {
            return;
        }

        $definition = $container->findDefinition(BookParser::class);
        $taggedServices = $container->findTaggedServiceIds('app.book_format');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFormat', [new Reference($id)]);
        }
    }
}