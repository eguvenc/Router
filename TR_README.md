
## Obullo Router

Obullo http router bağımsız php router paketidir. <kbd>Route grupları</kbd>, <kbd>Route filtreleri</kbd>, <kbd>Route middleware</kbd> gibi özelliklerin yanı sıra az kodlama ve yüksek performans hedefler.

```
require '../vendor/autoload.php';

use Obullo\Router\Router;
use Obullo\Router\MiddlewareQueue;
use Obullo\Router\Dispatcher;

$request  = Zend\Diactoros\ServerRequestFactory::fromGlobals();
$response = new Zend\Diactoros\Response;

$router = new Router($request, $response);
```

#### Kurallar

```php
$router->map('GET', '/', 'Welcome/index');
```

Bu route kuralları <kbd>"/"</kbd> yada <kbd>"/welcome"</kbd> istekleri geldiğinde <kbd>$handler</kbd> değişkeninden <kbd>"Welcome/index"</kbd> olarak çıktı elde edilmesini sağlar.

#### Çözümleme

```php
$dispatcher = new Dispatcher($request, $response, $router);
$handler 	= $dispatcher->execute();

if ($handler instanceof Zend\Diactoros\Response) {
    echo $handler->getBody().'<br>';
} else {
    var_dump($handler); // "Welcome/index"
}

var_dump($dispatcher->getArgs());  // Varsa map edilmiş argümanlar çıktılanır.
```

#### Http Tabanlı Kurallar

Eğer birden fazla http metodu tanımlamak isterseniz bu metotları bir dizi içerisinde tanımlamanız gerekir.


```php
$router->map(['GET','POST','PUT'], '/users/(.*)',
     function ($request, $response, $args) use($router) {

         $response->getBody()->write('Welcome user !');

       return $response;
});
```

* Tanımlanmayan bir http isteği geldiğinde middleware kuyruğuna <kbd>NotAllowed</kbd> middleware sınıfı eklenir.
* <kbd>MiddlewareQueue</kbd> kullanmak istemiyorsanız bu davranışı kendi <kbd>Dispatcher</kbd> sınıfınızı kullanarak değişterebilirsiniz.


#### Yeniden Yazım

```php
$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en
```

Eğer tüm route kuralları yukarıdaki gibi değiştirilmek isteniyorsa <kbd>rewrite</kbd> metodu en tepede kullanılır. Böylece mevcut kurallarda değişiklik yapmak zorunda kalmazsınız.


#### Kesin Türler Belirleme

```php
$router->map('GET', 'welcome/index/(?<id>\d+)/(?<month>\w+)', 'Welcome/index/$1/$2');
```

<kbd>$dispatcher->getArgs()</kbd> kullanılarak metodu ile dışarıdan argüman değerleri elde edilmiş olur.

```php
$router->map('GET', 'arguments/index/(?<id>\d+)/(?<month>\w+)',
	function($request, $response, $args) use($router) {
    	$response->getBody()->write(print_r($args, true));
    	return $response;
	}
);
```

<kbd>$args</kbd> değişkeni aşağıdaki gibi çıktılanır.

```php
/*
Çıktı
array(2) {
  "id" => 155
  [0]=>
  string(3) "155"
  "month" => "October"
  [1]=>
  string(2) "October"
}
*/
```

Başka bir örnek yazım

```php
$router->map('GET', '/users/(\w+)/(\d+)', '/Users/$1/$2');
$router->map('GET', '/users/(\w+)/(\d+)', function ($request, $response, $args) use($router) {
     var_dump($args);
});
```

#### Rest Tabanlı Kurallar

Router paketi varsayılan olarak web sunucu davranışları sergiler. 

```php
$router->restful(false);  // Restful davranışını devredışı bırak.
```

* Restful değeri <b>false</b> iken bir route kuralı ile eşleşmezse olmazsa route handler geçerli uri path değerine döner.
* Restful değeri <b>true</b> ilen bir route kuralı ile eşleşmezse olmazsa handler <b>NULL</b> değerine döner.


#### Kural Grupları

```php
$router->group(
    'group/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '/(\w+)/(\d+).*',
                    function ($request, $response, $args = null) use ($router) {
                    
                        $response->getBody()->write("It works !");

                        return $response;
                    }
                );
            }
        );
    }
);
```


#### Middleware Kullanmak

Eğer bir middleware kuyruklayıcı kullanmak istiyorsanız <kbd>MiddlewareQueue</kbd> sınıfını kullanabilirsiniz.

```php
$middlewareQueue = new MiddlewareQueue(new SplQueue);
```

Kuyruklayıcının çalışabilmesi middleware klasörünüzü register metodu ile belirlemeniz gerekir.

```php
$middlewareQueue->register('\App\Middleware\\');
```

Son olarak kuyruklayıcıyı execute metoduna enjekte edin.

```php
$handler = $dispatcher->execute($middlewareQueue);
```

Bir middleware <kbd>add</kbd> metodu kullanılarak aşağıdaki gibi bir route kuralına,

```php
$router->map('GET','(\w+)/(.*)')->add('Dummy');
```

veya bir gruba eklenebilir.

```php
$router->group(
    'test/',
    function ($request, $response) use ($router) {

        $router->map(
            'GET',
            'dummy.*',
            function ($request, $response, $args = null) use ($router) {
                $response->getBody()->write("It works !");
                return $response;
            }
        );
    }

)->add('Dummy');
```


#### Middleware Filtreleri

Middleware filtreleri kuralların yanısıra uri değeri filtrelenerek belirli şartlara uygunluk gösterip göstermemelerine göre eklenebilirler.

##### Contains Filtresi

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        
                        $response->getBody()->write("It works !");

                        return $response;
                    }

                )->filter('contains', ['test/foo/123', 'test/foo/1234'])->add('Dummy');
            }
        );
    }
);
```

Yukarıdaki filtre <kbd>example/test/(\w+)/(\d+).\*</kbd> eşleşmesinden sonra <kbd>test/foo/123</kbd> ve <kbd>test/foo/1234</kbd> içeren http isteklerine <b>Dummy</b> middleware sınıfını ekler.

##### NotContains Filtresi

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        
                        $response->getBody()->write("It works !");

                        return $response;
                    }

                )->filter('notContains', ['test/foo/888', 'test/foo/999'])->add('Dummy');
            }
        );
    }
);
```

Contains metodunun zıt yönlü filtresidir.

##### Regex Filtresi

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(\d+).*',
                    function ($request, $response) use ($router) {
                        
                        $response->getBody()->write("It works !");

                        return $response;
                    }

                )->filter('regex', '.*?abc/(\d+)')->add('Dummy');
            }
        );

    }
);
```

Yukarıdaki filtre <kbd>example/test/(\w+)/(\d+).\*</kbd> eşleşmesinden sonra <kbd>abc/digit</kbd> değer içeren http isteklerine <b>Dummy</b> middleware sınıfını ekler.

##### Not Regex Filtresi

```php
$router->group(
    'example/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                $router->map(
                    'GET',
                    '(\w+)/(.*)',
                    function ($request, $response) use ($router) {
                        
                        $response->getBody()->write("It works !");

                        return $response;
                    }

                )->filter('notRegex', '.*?abc/(\d+)')->add('Dummy');
            }
        );

    }
);
```

Regex metodunun zıt yönlü filtresidir.


#### Örnekler

Daha fazla örnek kurallar tanımlamalarını <kbd>/public</kbd> klasöründe bulabilirsiniz.