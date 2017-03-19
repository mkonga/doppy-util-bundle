# 1.4.0

* Removed user specific entries from .gitignore
* Code style fixes
* Added initial version of changes.md

# 1.3.0

* Adds a lot of configuration options to the bundle. Allowing features to be disabled, possible improving performance in some cases.

# 1.2.1

* Some cleanup and renaming of internal services.
* Fixes an autoload issue with the NullStopwatch

# 1.2.0

* Adds a NullStopwatch, allowing you to allways call the stopwatch instead of checking if it exists first.

# 1.1.1

* Adds support for PHP 7

# 1.1.0

* Fixes incorrect priority ordering of tagged services (it was inversed)
* Fixes composer dependency configuration to be more sensible.

# 1.0.3

* Makes creating a CompilerPass a bit more easy with an abstract service.

# 1.0.2

* Adds support for passing the name of the service instead of the service itself.

# 1.0.1

* Adds support for OptionsResolver when using tagged services.

# 1.0.0

* Initial version
