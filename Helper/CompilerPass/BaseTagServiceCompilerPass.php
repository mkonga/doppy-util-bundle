<?php

namespace Doppy\UtilBundle\Helper\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseTagServiceCompilerPass implements CompilerPassInterface
{
    /**
     * @var OptionsResolver
     */
    private $_optionsResolver = null;

    public function process(ContainerBuilder $containerBuilder)
    {
        // get main service
        try {
            $serviceDefinition = $this->getService($containerBuilder);
        } catch (ServiceNotFoundException $e) {
            // seems it is not there, nothing to do
            return;
        }

        // get tagged services
        $taggedServices = $this->getTaggedServices($containerBuilder);

        // loop through all tagged services
        $list = [];
        foreach ($taggedServices as $taggedServiceId => $tags) {
            // get the definition
            $taggedService = $containerBuilder->findDefinition($taggedServiceId);

            // maybe do something with it
            $this->adjustTaggedService($taggedService);

            // loop through all tags that matched
            foreach ($tags as $attributes) {
                // push id into attributes as well
                $attributes['service_id'] = $taggedServiceId;

                // resolve attributes
                $attributes = $this->resolveAttributes($attributes);

                // add it to list
                $list[] = array(
                    'id'         => $taggedServiceId,
                    'priority'   => $attributes['priority'],
                    'attributes' => $attributes
                );
            }
        }

        // sort what we got
        usort($list, function ($left, $right) {
            return $left['priority'] - $right['priority'];
        });

        // now make some calls
        foreach ($list as $taggedService) {
            $this->handleTag(
                $containerBuilder,
                $serviceDefinition,
                new Reference($taggedService['id']),
                $taggedService['attributes']
            );
        }
    }

    /**
     * Adds a method call to the main definition using information from the tagged service
     *
     * @param ContainerBuilder $containerBuilder
     * @param Definition       $serviceDefinition
     * @param Reference        $taggedServiceReference
     * @param array            $attributes
     * 
     * $attributes contains:
     * - id         The id of the service
     * - priority   
     * - alias      Specific alias if passed, otherwise the same as id
     * - any additional things you configured in the OptionsResolver
     *
     * @return mixed
     */
    abstract protected function handleTag(
        ContainerBuilder $containerBuilder,
        Definition $serviceDefinition,
        Reference $taggedServiceReference,
        $attributes
    );

    /**
     * Must return the main service that uses tags
     *
     * @param ContainerBuilder $containerBuilder
     *
     * @return Definition
     *
     * @throws ServiceNotFoundException
     */
    abstract protected function getService(ContainerBuilder $containerBuilder);

    /**
     * @param ContainerBuilder $containerBuilder
     *
     * @return array An array of tags with the tagged service as key, holding a list of attribute arrays.
     *
     * @see ContainerBuilder::findTaggedServiceIds
     */
    abstract protected function getTaggedServices(ContainerBuilder $containerBuilder);

    /**
     * If you want, you can do stuff to each tagged service
     *
     * @param Definition $definition
     */
    protected function adjustTaggedService(Definition $definition)
    {
        // no action
    }

    /**
     * Resolves the attributes of a tag to a set of attributes we are expecting
     *
     * @param array $attributes
     *
     * @return array
     */
    private function resolveAttributes($attributes)
    {
        $optionsResolver = $this->getOptionsResolver();
        return $optionsResolver->resolve($attributes);
    }

    /**
     * @return OptionsResolver
     */
    private function getOptionsResolver()
    {
        // create optionsresolver when needed
        if (empty($this->_optionsResolver)) {
            $this->_optionsResolver = new OptionsResolver();
            $this->configureOptionsResolver($this->_optionsResolver);
        }

        // return it
        return $this->_optionsResolver;
    }

    /**
     * Configures the OptionsResolver used for the attributes
     *
     * Override this method if you need something additional
     *
     * @param OptionsResolver $optionsResolver
     */
    protected function configureOptionsResolver(OptionsResolver $optionsResolver)
    {
        // some defaults
        $optionsResolver->setDefaults(array(
            'service_id' => '',
            'priority'   => 0,
            'alias'      => ''
        ));

        // set types
        $optionsResolver->setAllowedTypes('service_id', 'string');
        $optionsResolver->setAllowedTypes('priority', 'int');
        $optionsResolver->setAllowedTypes('alias', 'string');

        // some stuff is required
        $optionsResolver->setRequired(['service_id']);

        // alias
        $optionsResolver->setNormalizer('alias', function (Options $options, $value) {
            if (empty($value)) {
                return $options['service_id'];
            }
            return $value;
        });
    }
}