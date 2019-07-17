
# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.8 - 2019-07-17

Fixed Pipe matcher bug, added extra slash to $requestContext->getPath() method.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed Pipe matcher bug, added extra slash to $requestContext->getPath() method.


## 1.0.7 - 2019-06-08

The name of the 'types' key in the configuration entered in the route collection has been changed to 'patterns'.

### Added

- Added 'patterns' key to route collection configuration.
- getPatterns() method to route collection.

### Deprecated

- 'types' key in the route collection.
- getTypes() method in the route collection.

### Removed

- 'types' key in the route collection.
- getTypes() method in the route collection.

### Fixed

- Nothing.


## 1.0.6 - 2019-04-30

Match path trailing slash bug fixed.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Match path trailing slash bug fixed.

## 1.0.5 - 2019-02-16

Added url method to Router class. 

### Added

- Added add special route attributes functionality.
- Added setAttribute() and getAttribute() methods to route, router and pipe classes.
- Added AttributeAwareTrait class.
- The construct parameter of route class has been converted to array type to send route attributes as associative array.
- The construct parameter of pipe class has been converted to array type to send pipe attributes as associative array.

### Deprecated

- Deprecated adding route and pipe parameters one by one. Instead of we use one parameter as array.
- Deprecated StackAwareInferface.

### Removed

- UndefinedRouteNameException renamed as UndefinedRouteException.
- Removed Stack folder.

### Fixed

- Nothing.

## 1.0.4 - 2018-04-24

Added url method to Router class. 

### Added

- Added url method to Router class.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.


## 1.0.3 - 2018-04-17

Added configuration exception feature to RouteCollection class.

### Added

- Added RouteConfigurationException file.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.2 - 2018-03-29

Updated composer.json file.

### Added

- Nothing

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.1 - 2018-03-21

Added change log file, added getCollection method.

### Added

- Added getCollection() method to Router class.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Url generator default route "/" problem fixed.


## 1.0.0 - 2018-03-15

First beta, and first relase as `obullo/router`.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.