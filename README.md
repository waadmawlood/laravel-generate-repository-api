
# Generate Repository Design Pattern Api
## you can create your restful api easily by using this library with filtering sorting 

![Banner](https://raw.githubusercontent.com/waadmawlood/laravel-generate-repository-api/main/external/Laravel%20Generate%20Repository%20Api.png)

## Installation:
Require this package with composer using the following command:

```bash
composer require waad/laravel-generate-repository-api
```

```bash
php artisan vendor:publish --provider="Waad\Repository\RepositoryServiceProvider" 
```

in `config/app.php`
```bash
'providers' => [
    // ...
    Spatie\Permission\PermissionServiceProvider::class,
];
```

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

```bash
php artisan optimize:clear
php artisan migrate
```

⚠️ Change Default Guard from `config/auth.php` => `defaults.guard` if you want use differnet guard.


\
&nbsp;

in `config/database.php` if use search multi column

```js
'mysql' => [
    // .....
    'strict' => false,   // make false if used search parameter
    // .....
],
```


\
&nbsp;

## 🚀 About Me
I'm a Back End developer...

- Author :[ Waad Mawlood](https://waad.netlify.app/)

- Email  : waad_mawlood@outlook.com


## YAML Roadmap Job Of Package

```js
Package:
  app:
    DTO:
      className1:
        - className1Dto1.php
        - className1Dto2.php
      className2:
        - className2Dto1.php
        - className2Dto2.php
    Http:
      Controller:
        Api:
          - className1Controller.php
          - className2Controller.php
      Requests:
        className1:
          - StoreClassName1Request.php
          - UpdateClassName1Request.php
        className2:
          - StoreClassName2Request.php
          - UpdateClassName2Request.php
    Models:
      - ClassName1Model.php
      - ClassName2Model.php
    Policies:
      - ClassName1Policy.php
      - ClassName2Policy.php
    Interfaces:
      - ClassName1Interface.php
      - ClassName2Interface.php
    Repositories:
      - ClassName1Repository.php
      - ClassName2Repository.php
    Properties:
      className1:
        - className1Accessorable.php
        - className1Mutatorable.php
        - className1Propertable.php
        - className1Relatable.php
        - className1Scopable.php
      className2:
        - className2Accessorable.php
        - className2Mutatorable.php
        - className2Propertable.php
        - className2Relatable.php
        - className2Scopable.php
  config:
    - laravel-generate-repository-api.php
  database:
    migrations:
      - 15151515_create_{ClassNameTable1}_table
      - 15151517_create_{ClassNameTable2}_table
  routes:
    api.php: Add Route string inside file or
    web.php: Add Route string inside file

```



# Usage

### - Generating All
```bash
php artisan repo:model NameModel --a
```

if use force
```bash
php artisan repo:model NameModel --a --force
```

in all that will generate (`model`, `migration`, `controller api`, `repository`, `interface`, `properties`, `request forms`, `route string in api or web`)

### - Generating Some

```bash
php artisan repo:model NameModel                // generating model, properties
``` 

```bash
php artisan repo:model NameModel --force        // generating model, properties (override)
```

```bash
php artisan repo:model NameModel --m            // generating model, properties, migration
``` 

```bash
php artisan repo:model NameModel --c            // generating model, generating controller, interface, repository, Request forms
``` 

```bash
php artisan repo:model NameModel --p --guard=agent          // generating model, generating policy with agent guard if null take default guard
``` 

```bash
php artisan repo:model NameModel --m --c --p    // generating model, generating migration, controller, policy, interface, repository, Request forms
``` 

```bash
php artisan repo:model NameModel --m --c --p --force   // generating model, generating migration, controller, policy, interface, repository, Request forms (override)
``` 

```bash
php artisan repo:model NameModel --permission            // generating model, properties, add permissions to database with default guard
``` 

```bash
php artisan repo:model NameModel --permission --guard=agent      // generating model, properties, add permissions to database with agent guard
``` 

```bash
php artisan repo:model NameModel --permission --guard=agent,user      // generating model, properties, add permissions to database with multi guard
``` 

```bash
php artisan repo:model NameModel --r            // generating model, properties, add route in side api file (Route::apiResource('class_name', 'ClassNameController');)
``` 

```bash
php artisan repo:model NameModel --r --route=web           // generating model, properties, add route in side web file (Route::apiResource('class_name', 'ClassNameController');)
```

```bash
php artisan repo:model NameModel --r --route=web ---resource          // generating model, properties, add route in side web file (Route::resource('class_name', 'ClassNameController');)
```

```bash
php artisan repo:model NameModel --ns          // generating model, properties, without soft delete in model and restore force delete methods in controller
```

---
\
&nbsp;

### - After migration Create validation And DTO Files


create table columns in migration and do migration

&nbsp;

```bash
php artisan migrate
```

```bash
php artisan repo:validation NameModel       // generate StoreRequestForm And UpdateRequestForm in Http/Requests/NameModel/....  with DTO File in DTO/NameModel/.....
```

```bash
php artisan repo:validation NameModel --ndto       // generate StoreRequestForm And UpdateRequestForm in Http/Requests/NameModel/....  without DTO
```


---

&nbsp;

### - Create permissions of model into database customs
⚠️ use it if refresh database or drop permissions from database
```php
php artisan repo:permission Car                     // get default guard
````

```php
php artisan repo:permission Car --guard=api            // with guard
````

```php
php artisan repo:permission Car --guard=api,web       // with multi guard

or

php artisan repo:permission Car --guard="api, web"    // with multi guard
````



---
\
&nbsp;

### - Filtering, Sorting And Properties Of Model
 

#### Example In `properties/Car/CarPropertable` 

`if you add manually` 
```js
use CarPropertable;
```

- **fillable** : fill allowed fields to inserting and updating from `$fillable_override` array property

- **filtering** : fill allowed fields to filtering from `$filterable` array property that use `filter,find` parameter in request

- **sorting** : fill allowed fields to sorting from `$sortable` array property

- **Other** :

| &nbsp; Property in Model &nbsp; |&nbsp; Aliases in Propertable &nbsp;|
|------------------------|-------------------------------------|
| *$connection*          | $connection_override                |
| *$table*               | $table_override                     |
| *$primary*             | $primary_override                   |
| *$primary*             | $primary_override                   |
| *$timestamps*          | $timestamps_override                |
| *$incrementing*        | $incrementing_override              |
| *$keyType*             | $keyType_override                   |
| *$hidden*              | $hidden_override                    |
| *$dates*               | $dates_override                     |
| *$casts*               | $casts_override                     |
| *$guarded*             | $guarded_override                   |



---
\
&nbsp;

### - Accessor And Mutator Of Model
 

#### Example In `properties/Car/CarAccessorable`  And `properties/Car/CarMutatorable`

`if you add manually` 
```js
use CarAccessorable;
use CarMutatorable;
```

- **In Accessor and Mutator files there are some examples to create accessor And Mutator methods** 

- **Appending** : add attributes for response  from `$appends_override` array  in Accessorable file


---
\
&nbsp;

### - Scopes Of Model
 

#### Example In `properties/Car/CarScopable`

`if you add manually` 
```js
use CarScopable;
```

- **In Scopable file there are some examples to create scope methods** 



---
\
&nbsp;

### - Related Of Model
 

#### Example In `properties/Car/CarRelatable`

`if you add manually` 
```js
use CarRelatable;
```

- **In Relatable file there are some examples to create Relationship methods with other models** 

- **including** : model allow include (join) with other model Relationship by used `$includeable` array property example `user`, `user.city` when use `include=model,user.city` in request
- **Other** :

| &nbsp; Property in Model &nbsp; |&nbsp; Aliases in Relatable &nbsp;|
|------------------------|-------------------------------------|
| *$with*                | $with_override                      |
| *$withCount*           | $withCount_override                 |

⚠️  don't remove `use ConstructorableModel;` in **Relatable**




---
\
&nbsp;

### - Policy Of Controller
 

#### Example In `Policies/CarPolicy`

- **first** : Add Triats to any model used Authontication eg . `User`, `Admin` in Model

Example
```js
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Waad\Repository\Traits\HasAnyRolePermissions;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;                 // important
    use HasAnyRolePermissions;    // important
    ...............
```

if you choose generate policy in command line with creating policy for model and controller will call to it automatic from cosntructor of controller ex `$this->authorizeResource(Car::class, 'car');`

- if don't define policy because of named or another problem define it from `app/Providers/AuthServiceProvider.php`

example
```js

use App\Models\Car;
use App\Policies\CarPolicy;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Car::class => CarPolicy::class,
    ];

    
    public function boot()
    {
        $this->registerPolicies();

        // use super admin that don't check any permission if was Super Admin
        // you can change that from `config/laravel-generate-repository-api.php`
        Gate::before(function ($user, $ability) {
            return $user->hasRole(config('laravel-generate-repository-api.super_admin')) ? true : null;
        });
    }
}
```

🔥 Change Role Of Super from `config/laravel-generate-repository-api.php`

❤️ ```php artisan optimize ``` &nbsp; to clear cache of config


\
&nbsp;
\
&nbsp;


# Examples with Requests

\
&nbsp;

- http://127.0.0.1:8000/api/cars?include=user.city,model,color&sort=-date_made&select=id,name,age&except=age,notes

| &nbsp; Parameter &nbsp; |&nbsp; Cases &nbsp;|
|------------------------|-------------------------------------------|
| *include*              | string seperator "," comma                |
| *sort*                 | string with "-" DESC Order else ASC       |
| *select*               | return only select columns from table     |
| *except*               | return all columns of table exclude except|

- http://127.0.0.1:8000/api/cars?include=user.city&filter[user.city.name]=baghdad
=> `user.city.name LIKE baghdad`

| &nbsp; Parameter &nbsp; |&nbsp; Cases &nbsp;|
|------------------------|-------------------------------------|
| *filter*               | array search also with relationsip used `LIKE` Operator In Database |
| *search*               | search multi columns with relationsip used `LIKE` Operator In Database use `searchable` property |
| *strict*               | flag boolean used with `search` if equal `1` or `true` will return more data else return few |

- http://127.0.0.1:8000/api/cars?include=user.city&find[user.city.name]=baghdad&find[model.year]=lt:1995:or:gt:2020

=> `(user.city.name = baghdad) And (model.year < 1995 OR model.year > 2020)`


- find same filter but not use `Like` Operator by default  `Equal` `=`

| &nbsp; Operator &nbsp; |&nbsp; Aliases &nbsp;| &nbsp; Example &nbsp;|
|-----------|--------|------------------|
| *eq*      | =      | `find[user.name]=waad` `find[user.name]=eq:waad` |
| *ne*      | !=      | `find[user.name]=ne:waad`|
| *gt*      | >      | `find[user.age]=gt:50` `find[user.age]=gt:50:and:lt:100` |
| *ge*      | >=      | `find[user.age]=ge:50` `find[user.age]=ge:50:and:le:100` |
| *lt*      | <      | `find[user.age]=lt:50` `find[user.age]=lt:50:and:gt:10` |
| *le*      | <=      | `find[user.age]=le:50` `find[user.age]=le:50:and:ge:10` |
| *notlike*      | NOT LIKE      | `find[user.name]=notlike:waad` |
| *in*      | OR      | `find[user.age]=in:44,43` |
| *notin*      | NOT OR      | `find[user.age]=notin:44,43` |
| *null*      | NULL      | `find[user.name]=null` |
| *notnull*      | NOT NULL      | `find[user.name]=notnull` |
| *today*      | TODAY      | `find[car.created_at]=today` |
| *nottoday*      | NOT TODAY      | `find[car.created_at]=nottoday` |


😍 If you send values by `base64` example `find[user.age]={{b64(NTY=)}}` => `find[user.age]=56`



\
&nbsp;

#### - Use Pagination Or Unlimit Takes with Request

#### Use `take` and `page` Parameters

- **Pagination** : use `Pagination` request form with take 100 default eg.

```js
use App\Http\Requests\Pagination;

public function index(Pagination $pagination)
{
    return $this->CarRepository->index($pagination);
}
```

- **Unlimit** : use `Unlimit` request form with take 100 default if use take eg.

```js
use App\Http\Requests\Unlimit;

public function index(Unlimit $unlimit)
{
    return $this->CarRepository->index($unlimit);
}
```

\
&nbsp;



#### - Return Trashed Records if use soft delete

#### Use `trash` parameter including these values:

| &nbsp; Operator &nbsp; | &nbsp; Meaning &nbsp;
|-----------|--------|
| *current* &nbsp; or &nbsp; *NULL* &nbsp; or &nbsp; *empty*  | get records without trashed  | 
| *all*  | get all records with trashed  | 
| *trashed*  | get records only trashed  |

```js
// in Controller
public function index(Pagination $pagination)
{
    return $this->CarRepository->index($pagination, $pagination->trash);
}
```



\
&nbsp;


#### - Use DTO Model, Pagination and List Responses

- one model use direct DTO eg.

```js
// in controller or repository

use App\DTO\Car\CarDto;

public function show(Car $car)
{
    $car = $this->CarRepository->show($driver)->load('driver');

    return response()->json(new CarDto($car->toArray()));
}
```

- Pagination model use paginate DTO from package eg.

```js
// in controller or repository

use App\DTO\Car\CarDto;
use Waad\Repository\DTO\PaginationDto;

public function index($request)
{
    $result = $this->indexObject($request)->paginate();

    return new PaginationDto($result, CarDto::class);
}
```
OR

```js
// in controller or repository

use App\DTO\Car\CarDto;
use Waad\Repository\DTO\DTO;

public function index($request)
{
    $result = $this->indexObject($request)->paginate();

    return DTO::pagiantion($result, CarDto::class);
}
```

- List model use List DTO from package eg.

```js
// in controller or repository

use App\DTO\Car\CarDto;
use Waad\Repository\DTO\ListDto;

public function index($request)
{
    $result = $this->indexObject($request)->get();

    return new ListDto($result, CarDto::class);
}
```
OR
```js
// in controller or repository

use App\DTO\Car\CarDto;
use Waad\Repository\DTO\DTO;

public function index($request)
{
    $result = $this->indexObject($request)->paginate();

    return DTO::list($result, CarDto::class);
}
```



\
&nbsp;



## License

[MIT](https://choosealicense.com/licenses/mit/)
