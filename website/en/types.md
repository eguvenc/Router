
## Types

> Route types manage the arguments in a route more flexibly by increasing the security.

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

> Instead of the `Zend\Config`, you can use any configuration package that extends to the `ArrayAccess` class.

### Default types

<table>
    <thead>
        <tr>
            <th>Type</th>    
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

Following example shows the structure of `Int` type.

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

* The `%s` value in the `regex` variable makes it a `(?<id>\d+)` for a type such as `<int:id>`.
* The `toPhp` method converts the argument to the specified type before it is used in php.
* The `toUrl` method formats `%s` with `sprintf` and allows you to create url links when the `Generator` class is called.
* You can also create your own types by extending to the `Type` class.

The second parameter of the construct method can be used to obtain a new regular expression from the defined type functions.

```php
new SlugType('<slug:slug>');
new SlugType('<slug:slug_>', '(?<%s>[\w-_]+)');  // slug with underscore
```

You need to change the name of cloned type. In the above example, the slug type is cloned with the name `<slug:slug_>` to add underline support.