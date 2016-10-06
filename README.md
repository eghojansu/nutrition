# Nutrition

Add nutrition to [fatfree framework](https://github.com/bcosca/fatfree)

## Instalation

```
composer require eghojansu/nutrition
```

## Configuration

```
# user class that implement Nutrition\UserProviderInterface
SECURITY.provider = \UserClass
# session key
SECURITY.sessionKey = user

# default template
VIEW.template = app/view/template.html
# template key
VIEW.key = view

# in order to use SQL Mapper you need construct DB\SQL and assign as DB.SQL in global var
# but you can also set key in Mapper class too
DB.SQL = (DB\SQL) instance
# in order to use Jig Mapper you need construct DB\Jig and assign as DB.Jig in global var
DB.Jig = (DB\Jig) instance
# in order to use Mongo Mapper you need construct DB\Mongo and assign as DB.Mongo in global var
DB.Mongo = (DB\Mongo) instance
```
