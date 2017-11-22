Nutrition
=========

Add nutrition to [fatfree framework][1]


Instalation
-----------

```
composer require eghojansu/nutrition
```

Ingredients
-----------

This plugin add some functionality to base framework.

- Security (Authentication and Authorization)
- SQL Connection and Mapper extension
- Template Setup to handle template variables
- Simple FileSystem utility
- Route extension (add missing functionality on fatfree routing)
- Abstract Command, make simple controller being like symfony/command component
- Session Flash
- HTML Utility (Breadcrumb, Pagination)
- Data Validator

Configuration
-------------

This plugin add some global configuration.

```ini
[globals]
; Log file
LOG_FILE = null
ERROR_TEMPLATE = null

[DATABASE]
; database configuration
name = db_fal
username = root
password = null
host = 127.0.0.1
port = null

; Security configuration
[SECURITY]
user_class = null
user_provider = null
password_encoder = null

; Firewalls
[SECURITY.firewals.xxxNamexxx]
path = ^/admin
roles = ROLE_ADMIN
login_route = login_admin

; Role hierarchy
[SECURITY.role_hierarchy]
ROLE_B = ROLE_A
ROLE_C = ROLE_B

```

Documentation
-------------
Please refer to source-code-documentation to see detail.


Database/Mapper Name Case Usage
-------------------------------

This library developed with PascalCase concept for database and mapper, The mapper's table name will be pluralized. You can change this behaviour by override parent's method.


[1]: https://github.com/bcosca/fatfree
