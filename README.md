

## Obullo Router

Obullo http router bağımsız php router paketidir. <kbd>Route grupları</kbd>, <kbd>Route filtreleri</kbd>, <kbd>Route middleware</kbd> gibi özelliklerin yanı sıra az kodlama ve yüksek performans hedefler.

#### Rest Tabanlı Kurallar

Router paketi varsayılan olarak web sunucu davranışları sergiler. Eğer bir route kuralı ile eşleşmezse olmazsa route handler http isteğinden gelen uri path değerine döner.


```php
$router->restful(false);  // Restful davranışlarını devredışı bırak.
```

Fakat opsiyonel olarak rest tabanlı route sayfanın en başında ilan edilirse router sınıfı bir rest sunucu gibi davranır ve herhangi bir route kuralı ile eşleşmezse olmazsa handler <b>NULL</b> değerine döner.

#### Kurallar

```php
$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome', 'Welcome/index');
```

Bu route kuralları <kbd>"/"</kbd> yada <kbd>"/welcome"</kbd> istekleri geldiğinde <b>$handler</b> değişkeninden <kbd>"Welcome/index"</kbd> olarak çıktı elde edilmesini sağlar.

#### Çözümleme

```php
$dispatcher = new Dispatcher($request, $response, $router);
$handler    = $dispatcher->execute();

var_dump($handler);  // "Welcome/index"
```

#### Http Tabanlı Kurallar

Bu kural sadece http GET türü dışındaki istekler geldiğinde <b>MiddlewareQueue</b> sınıfına <b>NotAllowed</b> middleware sınıfını gönderir.

```php
$router->map('GET', '/users/(.*)',
     function ($request, $response, $args) use($router) {

         $response->getBody()->write('Welcome user !');

       return $response;
});
```

Eğer birden fazla http metodu tanımlamak isterseniz bu metotları bir dizi içerisinde tanımlamanız gerekir.


```php
$router->map(['GET','POST','PUT'], '/users/(.*)',
     function ($request, $response, $args) use($router) {

         $response->getBody()->write('Welcome user !');

       return $response;
});
```

* MiddlewareQueue kullanmak istemiyorsanız bu davranışı kendi Dispatcher sınıfınızı kullanarak değişterebilirsiniz.


#### Yeniden Yazım

```php
$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en
```

Eğer tüm route kuralları yukarıdaki gibi değiştirilmek isteniyorsa <b>rewrite</b> metodu en tepede kullanılır. Böylece mevcut kurallarda değişiklik yapmak zorunda kalmazsınız.


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

<b>$args</b> değişkeni aşağıdaki gibi çıktılanır.

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

Bir middleware aşağıda bir route kuralına,

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