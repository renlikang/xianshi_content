
#1.composer require tanghengzhi/apidoc:dev-master --dev

#2.config/web.php
```php
  //config/web.php
  
  $config = [
      ...
      'modules' => [
        'apidoc' => [
            'class' => 'tanghengzhi\apidoc\Module',
        ],
      ],
      ...
  ]
```

#3. config/params.php
```php

    'apidoc'=>[
        'scan_dir'=>[
            'app/controllers',
        ]
    ]

```
