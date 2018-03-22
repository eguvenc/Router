
## Türler

> Türler route kuralları içerisindeki argümanların daha esnek bir biçimde yönetilmesini sağlar ve güvenliği arttırır.

```php
$config = array(
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
```

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
            <td>http://example.com/&lt;any:any&gt;/</td>
            <td>string</td>
            <td>http://example.com/%s</td>
        </tr>
        <tr>
            <td>BoolType</td>
            <td>(?&lt;status&gt;[0-1])</td>
            <td>http://example.com/&lt;bool:status&gt;/</td>
            <td>boolean</td>
            <td>http://example.com/%01d</td>
        </tr>
        <tr>
            <td>FourDigitYearType</td>
            <td>(?&lt;year&gt;[0-9]{4})</td>
            <td>http://example.com/&lt;yyyy:year&gt;/</td>
            <td>integer</td>
            <td>http://example.com/%04d</td>
        </tr>
        <tr>
            <td>IntType</td>
            <td>(?&lt;id&gt;\d+)</td>
            <td>http://example.com/&lt;int:id&gt;/</td>
            <td>integer</td>
            <td>http://example.com/%d</td>
        </tr>
        <tr>
            <td>SlugType</td>
            <td>(?&lt;slug&gt;[\w-]+)</td>
            <td>http://example.com/&lt;slug:slug&gt;/</td>
            <td>string</td>
            <td>http://example.com/%s</td>
        </tr>
        <tr>
            <td>StrType</td>
            <td>(?&lt;name&gt;\w+)</td>
            <td>http://example.com/&lt;str:name&gt;/</td>
            <td>string</td>
            <td>http://example.com/%s</td>
        </tr>
        <tr>
            <td>TranslationType</td>
            <td>(?&lt;locale&gt;[a-z]{2})</td>
            <td>http://example.com/&lt;locale:locale&gt;/</td>
            <td>string</td>
            <td>http://example.com/%02s</td>
        </tr>
        <tr>
            <td>TwoDigitDayType</td>
            <td>(?&lt;day&gt;[0-9]{2})</td>
            <td>http://example.com/&lt;dd:day&gt;/</td>
            <td>integer</td>
            <td>http://example.com/%02d</td>
        </tr>
        <tr>
            <td>TwoDigitMonthType</td>
            <td>(?&lt;month&gt;[0-9]{2})</td>
            <td>http://example.com/&lt;mm:month&gt;/</td>
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

* `regex` değişkeni içerisindeki `%s` değeri `<int:id>` gibi bir türün `(?<id>\d+)` ifadesine dönüştürülmesini sağlar.
* `toPhp` metodu gelen argüman türünü php içerisinde kullanılmadan önce belirlenen türe dönüştürür.
* `toUrl` metodu `%s` biçimindeki değerleri `sprintf` ile biçimlendirir ve `Generator` sınıfı çağırıldığında url linkleri oluşturmanızı sağlar.
* `Type` sınıfına genişleyerek kendi türlerinizi de oluşturabilirsiniz.


Tanımlı tür fonksiyonlarından yeni bir düzenli ifade elde etmek için construct metodu ikinci parametresi kullanılabilir.

```php
new SlugType('<slug:slug>');
new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)');  // slug with underscore
```

Klonlanan türe ait ismi değiştirmeniz gerekir. Yukarıdaki örnekte slug türüne alt çizgi desteği ekleyebilmek için etiket `<slug:slug_>` olarak değiştirildi.