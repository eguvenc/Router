
# Obullo / Router

[![Build Status](https://travis-ci.org/obullo/Router.svg?branch=master)](https://travis-ci.org/obullo/Router)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/router.svg)](https://packagist.org/packages/obullo/router)

> Obullo router <a href="https://docs.djangoproject.com/en/2.0/topics/http/urls/">Django Url Dispatcher</a> kütüphanesinden ilham alınarak geliştirilmiş bağımsız bir route paketidir.

## Yükleme

``` bash
$ composer require obullo/router
```

## Host konfigürasyonu

[CONFIGURATION.md](CONFIGURATION.md)

## Gereksinimler

Bu versiyon aşağıdaki PHP sürümleri tarafından destekleniyor.

* 7.0
* 7.1
* 7.2

## Test

``` bash
$ vendor/bin/phpunit
```

## Hızlı başlangıç

```php
require '../vendor/autoload.php';

use Obullo\Router\Route;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Router;
use Obullo\Router\Types\{
    StrType,
    IntType
};
$configArray = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
    ]
);
$config = new Zend\Config\Config($configArray);
```

Psr7 İsteği

```php
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);
```

Route Kolleksiyonu

```php
$collection = new RouteCollection($config);
$collection->setContext($context);
$collection->add('home', new Route('GET', '/', 'App\Controller\DefaultController::index'));
$collection->add(
    'dummy',
    new Route(
        'GET',
        '/dummy/index/<int:id>/<str:name>',
        'App\Controller\DummyController::index'
        ['App\Middleware\Dummy::class']
    )
);
```

Route Çağırma

```php
$route = $collection->get('dummy');

echo $route->getHandler(); //  "App\Controller\DummyController::index"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/dummy/index/(?\d+)/(?\w+)"
echo $route->getStack()[0]; // App\Middleware\Dummy::class
```

Url Çözümleme

```php
$router = new Router($collection);

if ($route = $router->matchRequest()) {

    $handler = $route->getHandler();
    $args = array_merge(array('request' => $request), $route->getArguments());

    $response = null;
    if (is_callable($handler)) {
        $exp = explode('::', $handler);
        $class = new $exp[0];
        $method = $exp[1];
        $response = call_user_func_array(array($class, $method), $args);
    }
    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo $response->getBody();  // Çıktı DummyController::index
    }
}
```

App\Controller\DummyController örneği

```php
namespace App\Controller;

use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\RequestInterface as Request;

class DummyController
{
    public function index(Request $request)
    {
        return new HtmlResponse('DummyController::index');
    }
}
```

## Tür konfigürasyonu

> Türler route kuralları içerisindeki argümanların daha esnek bir biçimde yönetilmesini sağlar ve güvenliği arttırır.

```php
$configArray = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
        new StrType('<str:word>'),
        new AnyType('<any:any>'),
        new BoolType('<bool:status>'),
        new IntType('<int:page>'),
        new SlugType('<slug:slug>'),
        new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)'), // slug with underscore
    ]
);
$config = new Zend\Config\Config($configArray);
```

> `Zend\Config` paketi yerine `ArrayAccess` sınıfına genişleyen herhangi bir konfigürasyon paketi kullanabilirsiniz.

### Varsayılan türler

<table>
    <thead>
        <tr>
            <th>Tür</th>    
            <th>Regex</th>
            <th>Route</th>
            <th>Php</th>
            <th>Url</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>AnyType</td>
            <td>(?&lt;any&gt;.*)</td>
            <td>http://example.com/&lt;any:any&gt;</td>
            <td>string</td>
            <td>http://example.com/%s</td>
        </tr>
        <tr>
            <td>BoolType</td>
            <td>(?&lt;status&gt;[0-1])</td>
            <td>http://example.com/&lt;bool:status&gt;</td>
            <td>boolean</td>
            <td>http://example.com/%01d</td>
        </tr>
        <tr>
            <td>FourDigitYearType</td>
            <td>(?&lt;year&gt;[0-9]{4})</td>
            <td>http://example.com/&lt;yyyy:year&gt;</td>
            <td>integer</td>
            <td>http://example.com/%04d</td>
        </tr>
        <tr>
            <td>IntType</td>
            <td>(?&lt;id&gt;\d+)</td>
            <td>http://example.com/&lt;int:id&gt;</td>
            <td>integer</td>
            <td>http://example.com/%d</td>
        </tr>
        <tr>
            <td>SlugType</td>
            <td>(?&lt;slug&gt;[\w-]+)</td>
            <td>http://example.com/&lt;slug:slug&gt;</td>
            <td>string</td>
            <td>http://example.com/%s</td>
        </tr>
        <tr>
            <td>StrType</td>
            <td>(?&lt;name&gt;\w+)</td>
            <td>http://example.com/&lt;str:name&gt;</td>
            <td>string</td>
            <td>http://example.com/%s</td>
        </tr>
        <tr>
            <td>TranslationType</td>
            <td>(?&lt;locale&gt;[a-z]{2})</td>
            <td>http://example.com/&lt;locale:locale&gt;</td>
            <td>string</td>
            <td>http://example.com/%02s</td>
        </tr>
        <tr>
            <td>TwoDigitDayType</td>
            <td>(?&lt;day&gt;[0-9]{2})</td>
            <td>http://example.com/&lt;dd:day&gt;</td>
            <td>integer</td>
            <td>http://example.com/%02d</td>
        </tr>
        <tr>
            <td>TwoDigitMonthType</td>
            <td>(?&lt;month&gt;[0-9]{2})</td>
            <td>http://example.com/&lt;mm:month&gt;</td>
            <td>integer</td>
            <td>http://example.com/%02d</td>
        </tr>
    </tbody>
</table>

Aşağıda `Int` türüne ait bir örnek gösteriliyor.

```php
class IntType extends Type
{
    protected $regex = '(?<%s>\d+)';

    /**
     * Php format
     * 
     * @param  number $value 
     * @return int
     */
    public function toPhp($value)
    {
        return (int)$value;
    }

    /**
     * Url format
     * 
     * @param mixed $value
     * @return string
     */
    public function toUrl($value)
    {
        return sprintf('%d', $value);
    }
}
```

* `regex` değişkeni içerisindeki `%s` değeri `<int:id>` gibi bir türe ilişkin ismin `(?<id>\d+)` ifadesine dönüştürülmesini sağlar.
* `toPhp` metodu gelen argüman türünü php içerisinde kullanılmadan önce belirlenen türe dönüştürür.
* `toUrl` metodu `%s` biçimindeki değerleri `sprintf` ile biçimlendirir ve `UrlGenerator` sınıfı çağırıldığında url linkleri oluşturmanızı sağlar.
* `Type` sınıfına genişleyerek kendi türlerinizi de oluşturabilirsiniz.


Tanımlı tür fonksiyonlarından yeni bir düzenli ifade elde etmek için construct metodu ikinci parametresi kullanılabilir.

```php
new SlugType('<slug:slug>');
new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)');  // slug with underscore
```

Klonlanan ikinci türe ait ismi değiştirmeniz gerekir. Yukarıdaki örnekte slug türü `<slug:slug_>` ismi ile klonlanarak alt çizgi desteği eklendi. 

## Yükleyiciler

Uygulamanızı daha anlaşılabilir kılmak amacı ile route kolleksiyonunu  bir dosya içerisinde tutmak isteyebilirsiniz. Yükleyiciler uygulamanızda tanımlı olan bir route dosyasını okuyarak route kolleksiyon sınıfını oluştururlar.

### Yaml dosya yükleyicisi

Örnek bir .yaml dosyası

```
home: 
    path: /
    handler: App\Controller\DefaultController::index
admin/:
    host: example.com
    scheme: http
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /dummy/<str:name>/<int:id>
        handler: App\Controller\DefaultController::dummy
```

```php
require '../vendor/autoload.php';

use Obullo\Router\Route;
use Obullo\Router\RequestContext;
use Obullo\Router\RouteCollection;
use Obullo\Router\Router;
use Obullo\Router\Types\{
    StrType,
    IntType
};
$configArray = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
    ]
);
$config = new Zend\Config\Config($configArray);
```

Psr7 İsteği

```php
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$context = new RequestContext;
$context->fromRequest($request);

$collection = new RouteCollection($config);
$collection->setContext($context);
```

Yükleyici

```php
$loader = new YamlFileLoader;
$loader->load('/var/www/MyProject/App/routes.yaml');
$collection = $loader->build($collection);
```

Url Çözümleme

```php
$router = new Router($collection);

if ($route = $router->matchRequest()) {

}
```

### Php dosya yükleyicisi

Örnek bir .php dosyası

```
return [
    'home' => [
        'path'   => '/',
        'handler'=> 'App\Controller\DefaultController::index',
    ],
    'admin/' => [
        'host' => 'example.com',
        'scheme' => 'http',
        'middleware' => [App\Middleware\Auth::class],
        'dummy' => [
            'path'   => '/dummy/<str:name>/<int:id>',
            'handler'=> 'App\Controller\DefaultController::dummy',
        ]
    ]
];
```

Yükleyici

```php
$loader = new PhpFileLoader;
$loader->load('/var/www/MyProject/App/routes.php');
$collection = $loader->build($collection);
```

Bu kısımda sadece yükleyici değiştirmeniz yeterli olacaktır.


## Pipe ile gruplama

Bir api tasarlıyorsanız yada uygulamanız genişleyebilir bir uygulama ise pipe ile uygulamayı parçalara ayırmak uygulamanızın performansını arttırır. 

Örneğin `users/` adında bir veri yolu oluşturursak bir pipe bu gruba ait nitelikleri url parçaları üzerinde birleştirir.

```php
$pipe = new Pipe('users/', [App\Middleware\Dummy::class], '<str:name>.router');
$pipe->add('test', new Route('GET', '/test', 'App\Controller\DefaultController::test'));
```

Http isteği `users/` gelmediği sürece bu nesneye ait route kümeleri için `preg_match` operasyonu uygulanmaz ve uygulamanız parçalara ayrıldığı için performanstan kazanılmış olur.

```php
$collection = new RouteCollection($config);
$collection->setContext($context);

$collection->add('home', new Route('GET', '/', 'App\Controller\DefaultController::index'));

$pipe = new Pipe('users/example/', [App\Middleware\Dummy::class], '<str:name>.router');
$pipe->add('dummy', new Route('GET', '/<int:id>/<str:name', 'App\Controller\DefaultController::test'));
$collection->addPipe($pipe);
```

Eklenen bir route adına ait değerleri test etmek;

```php
$route = $collection->get('users/example/dummy');

echo $route->getHandler(); //  "App\Controller\DefaultController::test"
echo $route->getMethods()[0]; // GET
echo $route->getPattern(); //  "/users/example/(?\d+)/(?\w+)"
```

### Yaml içinde pipe

Eğer en yüksek seviyedeki bir kuralın son karakteri forward slash `"/"` ile bitiyorsa bu kural pipe olarak işlem görür.  Örnek `"users/"`.

```
users/:
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /<int:id>/<str:name>
        handler: App\Controller\DefaultController::test
```

## Url Çözümleme

### MatchRequest

MatchRequest metodu `Psr7 Request` nesnesi üzerinden gelen bir http isteğini çözümler. Aşağıdaki gibi bir url yapısına sahip olduğumuzu düşünelim.

```
http://example.com/<int:id>
http://example.com/<int:id>/<str:name>/
```

Opsiyonel parametre için tanımlar

```php
$collection->add(
    'dummy/id',
    new Route('GET', '/<int:id>', 'App\Controller\DummyController::index')
);
$collection->add(
    'dummy/id/name',
    new Route('GET', '/<int:id>/<str:name>', 'App\Controller\DummyController::index')
);
```
Eşleşme

```php
$router = new Router($collection);

if ($route = $router->matchRequest()) {

    $handler = $route->getHandler();
    $methods = $route->getMethods();

    if (! in_array($request->getMethod(), $methods)) {
        throw new Exception(
            sprintf('Method %s is not allowed.', $request->getMethod())
        );
    }
    $args = array_merge(array('request' => $request), $route->getArguments());

    // Parse handlers
    $response = null;
    if (is_callable($handler)) {
        $exp = explode('::', $handler);
        $class = new $exp[0];
        $method = $exp[1];
        $response = call_user_func_array(array($class, $method), $args);
    }

    // Emit response
    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo $response->getBody();
    }
}
```

App\Controller\DummyController sınıfı

```php
namespace App\Controller;

use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\RequestInterface as Request;

class DummyController
{
    public function index(Request $request, int $id, $name = '')
    {
        return new HtmlResponse(
            'Variables: #id:'.sprintf('%d', $id).' #name:'.sprintf('%s', $name)
        );
    }
}
```

> ReflectionClass sınıfı kullanarak kontrolör sınıfı çözümlenirken dependency injection yöntemi ile bu sınıfa ait nesneleri otomatik çağırabilirsiniz.


### Match

Match metodu `Psr7 Request` nesnesi olmadan eşleşme gerçekleştirmeye olanak sağlar. Aşağıdaki gibi bir url yapısına sahip olduğumuzu düşünelim.

```
http://example.com/<str:name>/<int:id>
```

```php
$collection = new RouteCollection($config);
$collection->setContext($context);
$collection->add(
    'dummy',
    new Route(
        'GET',
        '/dummy/<str:name>/<int:id>',
        'App\Controller\DefaultController::dummy'
    )
);
```

Eşleşme

```php
$router = new Router($collection);

if ($route = $router->match('/dummy/test/55','example.com','http')) {

    $args = $route->getArguments();

    var_dump($args['name']); // string "test"
    var_dump($args['id']); // integer 55

    echo $route->getMethods()[0]; // GET
    echo $route->getHandler(); // App\Controller\DefaultController::dummy
}
```

## Koşullu tanımlamalar

Koşullu tanımlamalar eşleşmeyi koşula bağlarlar. Koşullar bir route yada pipe nesnesi için tanımlanabilir.

### Host

Aşağıdaki route kuralının çalışabilmesi `test.example.com` host eşleşmesine bağlıdır.

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [],
        'test.example.com'
    )
);
```

Aşağıdaki route kuralının çalışabilmesi  `(?<name>\w+).example.com` host eşleşmesine bağlıdır.

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [],
        '<str:name>.example.com'
    )
);
```

Host değeri eğer bir düzenli ifade ise `$router->getMatchedHosts()` metodu ile host eşleşmeleri alınabilir.

```php
$router = new Router($collection);

if ($router->matchRequest()) {
    echo $router->getMatchedHosts()[0]; // test.example.com
    echo $router->getMatchedHosts()['name']; // test
}
```

Pipe için bir örnek

```php
$pipe = new Pipe('test/','App\Middleware\Dummy','<str:name>.example.com',['http','https']);
```

### Scheme

Bir route yada pipe kuralının son parametresi eşleşmeyi uri scheme koşuluna bağlar.

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [],
        'test.example.com'
        ['http', 'https']
    )
);
```

Yaml içinde,

```
admin/:
    host: admin.example.com
    scheme:
        - http
        - https
    middleware:
        - App\Middleware\Auth
    dummy:
        path: /dummy/<str:name>
        handler: App\Controller\UserController::dummy
```

### Koşullu yükleyici

Herbir alt domain adına ait farklı route dosyası yükleyen bir yükleyici.

```php
$subdomain = strstr($context->getHost(), '.example.com', true); // admin

$loader->load('/var/www/MyProject/'.$subdomain.'_routes.yaml');
$collection = $loader->build($collection);
```

## Katmanlar

### Middleware eklemek

Router sınıfı dördüncü parametresi bir router kuralına,

```php
$collection->add('dummy',
    new Route(
        ['GET','POST'],
        '/dummy/(?<name>\w+)',
        'App\Controller\DefaultController:index',
        [App\Middleware\Dummy],
    )
);
```
pipe sınıfı ikinci parametresi ise bir pipe nesnesine http katmanı tayin eder.

```php
$pipe = new Pipe('test/','App\Middleware\Dummy','<str:name>.example.com',['http','https']);
```

Yaml içinde,

```
users/:
    middleware: 
        - App\Middleware\Dummy
    dummy:
        path: /<int:id>/<str:name>
        handler: App\Controller\DefaultController::test
        middleware: App\Middleware\Test
```

### Middleware dizisini almak

Router nesnesi `getStack` metodu route kolleksiyonuna atanan tüm middleware sınıflarına ulaşmanızı sağlar.

```php
if ($router->matchRequest()) {
    print_r($router->getStack()); // Array ( [0] => App\Middleware\Dummy)
}
```

## Yerelleştirme

Route kurallarınıza çoklu dil desteği eklemek için `TranslationType` sınıfının konfigüre edilmesi gerekir. 

```php
$configArray = array(
    'types' => [
        new IntType('<int:id>'),
        new StrType('<str:name>'),
        new TranslationType('<locale:locale>'),
    ]
);
$config = new Zend\Config\Config($configArray);
```

Dilleri belirli değerlere sınırlamak için ikinci parametreyi kullanabilirsiniz.

```php
new TranslationType('<locale:locale>', '(?<%s>(en|de|es))');
```

Aşağıdaki gibi bir url yapımız olduğunu varsayalım.

```
http://example.com/en/dummy/test
```

```php
$collection->add(
    'dummy',
    new Route(
        'GET',
        '/<locale:locale>/dummy/<str:name>',
        'App\Controller\DummyController::test'
    )
);
```

Eşleşen route kuralına ait yerel argümanı $request nesnesine ekleyebilirsiniz. Yerel değer kopyalandıktan sonra argümanlardan aşağıdaki gibi silinmelidir.

```php
if ($route = $router->matchRequest()) {
    $locale  = $route->getArgument('locale');
    $request = $request->withAttribute('locale', $locale);
    $route->removeArgument('locale');
}
```

Tam örnek 

```php
if ($route = $router->matchRequest()) {

    $response = null;
    $locale  = $route->getArgument('locale');
    $request = $request->withAttribute('locale', $locale);
    $route->removeArgument('locale');

    $args = array_merge(array('request' => $request), $route->getArguments());
    
    $handler = $route->getHandler();

    // Parse handlers
    if (is_callable($handler)) {
        $exp = explode('::', $handler);
        $class = new $exp[0];
        $method = $exp[1];
        $response = call_user_func_array(array($class, $method), $args);
    }

    // Emit response
    if ($response instanceof Psr\Http\Message\ResponseInterface) {
        echo $response->getBody();
    }
}
```

App\Controller\DummyController sınıfı

```php
namespace App\Controller;

use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\RequestInterface as Request;

class DummyController
{
    public function test(Request $request)
    {
        $locale = $request->getAttribute('locale');

        return new HtmlResponse('DummyController::test #locale:'.sprintf('%02s', $locale));
    }
}
```

## Url üretici

Url üreticisi türler içerisinde `toUrl` metodunu kullanarak güvenli url adresleri yaratır.

```php
public function toUrl($value)
{
    return sprintf('%d', $value);
}
```

Bir örnek.

```php
$collection->add(
    'dummy/name',
    new Route(
        'GET',
        '/<locale:locale>/dummy/<str:name>',
        'App\Controller\DefaultController::dummy'
    )
);
```

Url generator sınıfına parametre gönderelim.

```php
$generator = new UrlGenerator($collection);
echo $generator->generate('dummy/name', ['locale' => 'en', 'name' => 'test']);
```

Yukarıdaki örneğin çıktısı

```php
// '/en/dummy/test'
```

Başka bir örnek.

```php
$collection->add(
    'dummy/name/id',
    new Route('GET', '/<str:name>/<int:id>', 'App\Controller\DummyController::index')
);
```

```php
$generator = new UrlGenerator($collection);
echo $generator->generate('dummy/name/id', 'name' => 'test', 'id' => 5]);
```

Yukarıdaki örneğin çıktısı

```php
// '/test/5'
```


## Performans

### Obullo router paketi (Zend Diactoros ile)

100 route testi, eşleşmenin en son route ile olması (en kötü senaryo)

```
ab -n 1000 -c 100 http://router/dummy/index/850/test
```

```
Server Software:        Apache/2.4.27
Server Hostname:        router
Server Port:            80

Document Path:          /dummy/index/850/test
Document Length:        22 bytes

Concurrency Level:      100
Time taken for tests:   2.680 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      189000 bytes
HTML transferred:       22000 bytes
Requests per second:    373.09 [#/sec] (mean)
Time per request:       268.028 [ms] (mean)
Time per request:       2.680 [ms] (mean, across all concurrent requests)
Transfer rate:          68.86 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    2   6.3      0      26
Processing:    15  255  34.4    260     356
Waiting:        8  249  36.0    254     348
Total:         30  258  31.8    261     357

Percentage of the requests served within a certain time (ms)
  50%    261
  66%    267
  75%    270
  80%    272
  90%    280
  95%    294
  98%    324
  99%    344
 100%    357 (longest request)
```

### Hız üzerine odaklanmış diğer route paketleri (Zend Diactoros ile)

100 route testi, eşleşmenin en son route ile olması (en kötü senaryo)

```
ab -n 1000 -c 100 http://router/dummy/index/850/test
```

```
Server Software:        Apache/2.4.27
Server Hostname:        router
Server Port:            80

Document Path:          /dummy/index/850/test
Document Length:        37 bytes

Concurrency Level:      100
Time taken for tests:   2.455 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      204000 bytes
HTML transferred:       37000 bytes
Requests per second:    407.41 [#/sec] (mean)
Time per request:       245.455 [ms] (mean)
Time per request:       2.455 [ms] (mean, across all concurrent requests)
Transfer rate:          81.16 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    1   4.1      0      16
Processing:    13  233  41.7    242     365
Waiting:        8  228  40.9    236     362
Total:         22  234  38.7    242     365

Percentage of the requests served within a certain time (ms)
  50%    242
  66%    248
  75%    252
  80%    255
  90%    261
  95%    266
  98%    275
  99%    287
 100%    365 (longest request)
```

## Sonuç

Görüldüğü gibi Obullo router paketinin performansı hız üzerine odaklanmış diğer route paketleri ile hemen hemen aynıdır. Obullo router paketi uygulamanın anlaşılabilirliğini kolaylaştırmak ve kaynakları en az kullanarak yüksek performans elde edebilmek amacıyla tasarlanmıştır. Obullo opsiyonel route sorununu route ları alt alta yazarak uygulama içerisinde çözer.

Bir uygulamanın performansı önce insan zihninde başlar. Daha fazla performans elde etmek için uygulamanızı tasarlarken Pipe nesnesi ile route kümelerini gruplara ayırın. Her bir route kümesi için maksimum route sayısının 50-100 arasında olmasına özen gösterin.