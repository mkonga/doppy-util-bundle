# Doppy Util Bundle

A Symfony2/Symfony3 bundle containing some functionality that might be useful.

# What is in here?

## Temp file generator

Generate a filename in the configured temp dir:
````
$fileGenerator = $this->getContainer()->get('doppy_util.temp_file_generator');
$tempFileName = $fileGenerator->getTempFileName('think_of_something');
````

You can specifify a path in the configuration where to create the tempfiles, like shown below.
When you don't configure this, the result of sys_get_temp_dir is used.

````
doppy_util:
    temp_file:
        path: /your/path/
````

Using the default configuration, generated tempfiles will be removed on the terminate event. You can disable this using the configuration below.
You can also pass false as a second parameter to the generator to prevent that file from being cleaned up.
 
````
doppy_util:
    temp_file:
        cleanup_on_terminate: false
````

## Prepend config helper

Loading additional yml files for your config.yml made easier. Just add the following to your Extension class:

````
class DoppyUtilExtension extends Extension implements PrependExtensionInterface
{
    use PrependConfigTrait;

    public function prepend(ContainerBuilder $container)
    {
        $this->prependConfig($container, __DIR__ . '/../Resources/config/prepend.config.yml');
    }
}
````
Be careful what you add in there, as it is hard to remove it via the application config.
It is best to only use it for things you know you allways need.

## Tagged Services BaseClass

When you want to tag services it can be a bit of work to create a CompilerPass to get this working correctly.
As this is repetitive for simple cases, this base class might come in useful.

Create a CompilerPass class like this:

````
class YourCompilerPass extends BaseTagServiceCompilerPass
{
    protected function handleTag(
        ContainerBuilder $containerBuilder,
        Definition $serviceDefinition,
        Reference $taggedServiceReference,
        $attributes
    )
    {
        $serviceDefinition->addMethodCall('someMethod', array($taggedServiceReference));
    }

    protected function getService(ContainerBuilder $containerBuilder)
    {
        // return the main service you are tagging for
        return $containerBuilder->findDefinition('your-service-name');
    }

    protected function getTaggedServices(ContainerBuilder $containerBuilder)
    {
        // return all services that you need to do something with. usually with a specific tag
        return $containerBuilder->findTaggedServiceIds('your-tag-name');
    }
}
````

If you add an attribute "priority" to your tagged services, they will be automatically sorted.

An OptionsResolver is used to resolve all the attributes. This means you are restricted to what is configured.
You can add additional configuration by overriding the `configureOptionsResolver` method. See the class for more information.

## NullStopwatch

When using a stopwatch in development, it can be an hassle to write all the if-statements around the calls to the stopwatch.

Add the following configuration, and a service `debug.stopwatch` will always be available.
When the debug version from symfony is not configured, a dummy implementation is loaded that simply does nothing.

````
doppy_util:
    nullstopwatch: true
````

Keep in mind that you may get some weird results when you retrieve information from the Stopwatch, as the NullStopwatch has to make something up.
When enabled, you can always use the service `doppy_util.nullstopwatch`, to get the null version, even in development.
