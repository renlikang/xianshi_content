
#1.composer require daodao97/apidoc:dev-master --dev

#2.config/web.php
```php
  //config/web.php
  
  $config = [
      ...
      'modules' => [
        'apidoc' => [
            'class' => 'daodao97\apidoc\Module',
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
