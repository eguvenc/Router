
# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.5.0 - 2019-12-19

- Added variable add feature.

In .yaml file

```yaml
$var:
    middleware:
        - App\Middleware\Test
```

In .php file

```php
$collection->addVariable('$var', ['middleware' => ['App\Middleware\Test']]);
$var = $collection->getVariable('var');

print_r($var); // array('middleware' => array('App\Middleware\Test'))
```

### Added

- Added addVariable() and getVariable() methods to RouteCollection class.
- Added string $var support to Builder class.
- Added PathTranslationException.
- Added UndefinedVariableException.

### Deprecated

- Nothing.

### Removed

- BadParameterException.
- ParseException.
- SegmentTranslationException.

### Fixed

- Nothing.


## 1.4.1 - 2019-10-11

- Added  "/" support to Generate method of the Generate class.

```php
$url = (new Obullo\Router\Generator($collection))
    ->generate('/');

// output: "/"
```

### Added

- Nothing.

### Deprecated


- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.


## 1.4.0 - 2019-09-28

- Added TranslatableRouteCollection class, now you can translate your route rules.

An example routes.yaml file.

```yaml
/{hello}/{world}/<str:name>:
        handler: App\Controller\HelloController::dummy
```

An example php file.

```php
use Zend\I18n\Translator\Translator;

$translator = new Translator;
$translator->setLocale('en');
$translator->addTranslationFilePattern('PhpArray', ROOT, '/var/messages/%s/routing.php');

$collection = new TranslatableRouteCollection($pattern);
$collection->setContext($context);
$collection->setTranslator($translator);

use Symfony\Component\Yaml\Yaml;

$builder = new Builder($collection);
$collection = $builder->build(Yaml::parseFile('/var/www/Router/App/routes.yaml'));
```

English visitors:

```
http://en.example.com/hello/world/test
```

German visitors:

```
http://de.example.com/hallo/welt/test
```

- Generator class "generate()" method second parameter changed as array.
- Added $locale as last parameter to generate translatable urls.

```php
$url = (new Obullo\Router\Generator($collection))
    ->generate('/{user}/<str:name>/<int:id>', ['name', 5], 'de');

// /benutzer/name/5
```
 
- Added TranslatorInterface class.

```php
interface TranslatorInterface
{
    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate($message, $textDomain = 'default', $locale = null);
}
```

### Added

- Added TranslatorInterface class.
- Added TranslatableRouteCollection class.
- Added SegmentTranslationException class.

### Deprecated

- Use of arguments to the "generate()" method of the generator class.

### Removed

- Nothing.

### Fixed

- Nothing.


## 1.3.0 - 2019-08-24

- Generator class "generate()" method functionality changed like php sprintf().

```php
$url = (new Obullo\Router\Generator($collection))
    ->generate('/test/<str:name>/<int:id>', 'name', 5);

// /test/name/5
```

- Updated GeneratorInterface "generate()" method.
- Renamed Pattern class getTypes() method as getPatternTypes(), added getTaggedTypes() method.

### Added

- getPatternTypes();
- getTaggedTypes();

methods to Pattern class.

### Deprecated

- Generator generate method arguments.
- Pattern class getTypes() method.

### Removed

- Nothing.

### Fixed

- Nothing.


## 1.2.0 - 2019-07-25

Added Pattern class to easily manage route patterns.

### Added

- Added Pattern class to easily manage route patterns.
- Added parameters to Route class constructor to use as one by one.

```php
$pattern = new Pattern;
$pattern->add(new IntType('<int:id>'));
$pattern->add(new StrType('<str:name>'));
$pattern->add(new SlugType('<slug:slug>'));
$pattern->add(new TranslationType('<locale:locale>'));

$collection = new RouteCollection($pattern);
```
- StackAwareTrait renamed as MiddlewareAwareTrait.
- Added RouteCollection->middleware(),RouteCollection->host(),RouteCollection->scheme() methods.

```php
$collection->add(new Route('GET', '/test/index', 'Views/test.phtml'))
    ->host('example.com');
    ->scheme('http');
    ->middleware(App\Middleware\Dummy::class);
```

### Deprecated

- Deprecated first parameter of RouteCollection->add() method.
- Deprecated Route class construct parameters. All parameters can be entered one by one.

```php
$collection->add(new Route('GET', '/', 'Views/default.phtml'));
```

### Removed

- Removed StackAwareTrait class.
- Removed RouteConfigurationException class.
- Removed getStack() method from Router and StackAwareTrait classes.

### Fixed

- Nothing.

## 1.1.0 - 2019-07-19

Deprecated Pipe class and route attriburtes.

### Added

- Nothing.

### Deprecated

- Deprecated pipe functionality.
- Depreacated route names.

Changes have been made to create routes for yaml files in the following new format.

```yaml
/: 
    method: GET
    handler: App/src/Pages/welcome.phtml

/<locale:locale>: 
    handler: App/src/Pages/welcome.phtml
    middleware: App\Middleware\LocaleMiddleware

/user-guide/<str:version>/index.html:
    handler: App/src/Pages/user-guide.phtml
    middleware: App\Middleware\ValidateDocVersionMiddleware
```

Changes have been made to create routes for php files in the following new format.

```php
return [
	'/' => [
		'handler'=> 'App\Controller\DefaultController::index',
		'middleware' => 'App\Middleware\Dummy'
	],
	'/<locale:locale>/dummy/<str:name>' => [
		'handler'=> 'App\Controller\DefaultController::dummy',
	],
];
```

### Removed

- Pipe class.
- PipeInterface.
- PipeMatcher class.
- AttributeAwareTrait.

### Fixed

- Nothing.


## 1.0.8 - 2019-07-17

Fixed Pipe matcher bug, added extra slash to $requestContext->getPath() method.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed Pipe class scheme matching, converted scheme variable to array.
- Fixed Pipe matcher route matching, added extra slash to $requestContext->getPath() method.


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