# Version 11.0.0

## Bugfixes

* None

## Features

* Remove unnecessary use statement that results in a can not redeclare class fatal error

# Version 10.0.1

## Bugfixes

* None

## Features

* Remove unnecessary use statement that results in a can not redeclare class fatal error

# Version 10.0.0

## Bugfixes

* None

## Features

* Add generic provisioning functionality with steps for database, fixtures and migrations handling

# Version 9.0.0

## Bugfixes

* Refactoring to provide better override options in project specific customizations

## Features

* None

# Version 8.0.0

## Bugfixes

* None

## Features

* Extract default migration filenames + add getter
* Add use statement for EPB annotations and switch annotation from @Migrate to @EPB\Migrate in migrations template

# Version 7.0.1

## Bugfixes

* None

## Features

* Switch to appserver-io-psr/epb ~7.0 + appserver-io/description ~12.0
* Use Doctrine Annotations in AbstractMigration class

# Version 7.0.0

## Bugfixes

* Add DiffCommand to extend from GenerateCommand of this library

## Features

* Add getter for Application in ConfigurationHelper implementation

# Version 6.0.1

## Bugfixes

* Fixed issue with invalid usage of array_search when trying to purge tables, replace with in_array

## Features

* None

# Version 6.0.0

## Bugfixes

* Fixed issue with passing client side options though to server commands

## Features

* None

# Version 5.0.3

## Bugfixes

* None

## Features

* Switch default value parameter size of fixtures:load command from small to medium

# Version 5.0.2

## Bugfixes

* Fixed invalid use statement for interface FixtureDescriptorInterface in Loader class

## Features

* None

# Version 5.0.1

## Bugfixes

* None

## Features

* Switch to latest appserver-io/description version 11.0.0 and appserver-io-psr/epb version 6.0.0

# Version 5.0.0

## Bugfixes

* None

## Features

* Remove dependency to appserver-io, package has now to be delivered with application itself
* Move annotations and descriptors to appserver-io-psr/epb and appserver-io/description library

# Version 4.0.0

## Bugfixes

* Fixed issus when purging tables with relations
* Fixed missing entity manager helper for migrations:diff command

## Features

* None

# Version 3.0.0

## Bugfixes

* None

## Features

* Add a console command to load Doctrine Fixtures

# Version 2.0.0

## Bugfixes

* Fixed invalid instanciation of Version instances

## Features

* None

# Version 1.0.1

## Bugfixes

* Fixed invalid DocBlock

## Features

* None

# Version 1.0.0

## Bugfixes

* None

## Features

* Initial Release