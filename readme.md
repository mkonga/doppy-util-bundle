# Doppy Util Bundle

A Symfony2/Symfony3 bundle containing some functionality that might be useful.

# What is in here?

## Temp file generator

Generate a filename in the configured temp dir:
````
$fileGenerator = $this->getContainer()->get('doppy_util.temp_file_generator');
$tempFileName = $fileGenerator->getTempFileName('think_of_something');
````
The resulting file will be automatically removed at the end of the request when symfony is "shutting down".

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

## Tagged Services helper

When you want to tag services it can be a bit of work to create a CompilerPass to get this working correctly.
As this is repetitive for simple cases, this trait might save you some time.

Create a CompilerPass class like this:

````
class YourCompilerPass implements CompilerPassInterface
{
    use TaggedServicesTrait;

    public function process(ContainerBuilder $container)
    {
        $this->processTaggedServices($container, 'doppy_util.your_tag_and_service', 'addMethod', false);
    }
}
````
The service named `doppy_util.your_tag_and_service` will be fetched from the container,
and all other services tagged with the same name will be passed into the main service using the method `addMethod`.

Please note that the main service and the tag used must have the same name.

The following additional attributes on the tag can be used:

* `priority`: to determine the order (lowest to highest).
* `alias`: A second parameter is passed to the method which either contains this value, or the name of the service that was tagged.

Optionally, you can pass a fourth boolean parameter to make the tagged services lazy automatically.