<?php

namespace Doppy\UtilBundle\Helper\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait TaggedServicesTrait
{
    /**
     * @var OptionsResolver
     */
    private $_optionsResolver = null;

    protected function processTaggedServices(ContainerBuilder $container, $name, $method, $forceLazy = false)
    {
        // do nothing when the service is not found
        if (!$container->has($name)) {
            return;
        }

        // retrieve main service
        $serviceDefinition = $container->findDefinition($name);

        // retrieve tagged services
        $taggedServices = $container->findTaggedServiceIds($name);

        // prepare a resulting list
        $list = [];
        foreach ($taggedServices as $id => $tags) {
            // make lazy?
            if ($forceLazy) {
                $taggedService = $container->findDefinition($id);
                $taggedService->setLazy(true);
            }

            foreach ($tags as $attributes) {
                // push id into attributes as well
                $attributes['service_id'] = $id;

                // resolve attributes
                $attributes = $this->resolveAttributes($attributes);

                // add it to list
                $list[] = array(
                    'id'         => $id,
                    'priority'   => $attributes['priority'],
                    'attributes' => $attributes
                );
            }
        }

        // sort what we got
        usort($list, function ($left, $right) {
            return $left['priority'] - $right['priority'];
        });

        // now add them in requested order
        foreach ($list as $taggedService) {
            // add the MenuBuilder to our repository
            $serviceDefinition->addMethodCall(
                $method,
                array(
                    new Reference($taggedService['id']),
                    $taggedService['attributes']['alias']
                )
            );
        }
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