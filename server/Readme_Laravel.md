# [Laravel](https://laravel.com/)

[Laravel readouble](https://readouble.com/laravel/11.x/en/)
[Available Column Types](https://laravel.com/docs/11.x/migrations#available-column-types)

# Laravel projekt létrehozás

1. Laravel **laravel-rest-api** nevű (ez bármi lehet, ez lesz a projekt mappája) projekt létrehozása: `composer create-project laravel/laravel laravel-rest-api`

    - **laravel-rest-api** lesz a projekt mappája
    - Átmenni a **laravel-rest-api** mappába
    - Ellenőrzés: `php artisan serve`

2. Kapcsolódás az adatbázishoz: .env fájlban:
   Az adatbázis neve: laravel-rest-api

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel-rest-api
DB_USERNAME=root
DB_PASSWORD=
```

2. Adatbázis kódolás beállítás (ez az alap, nem kell hozzányúlni):
[Helyes adatbázis kódolás](https://gemini.google.com/share/4ef922c14e85)

Kódolás összehasonlítás:
- **utf8**: csak 3 bájtos ékezetes betűk (elavult)
- **utf8mb4**: 4 bájtos ékezetes betűk (**ajánlott**)
- **utf8mb4_hugarian_ci**: 4 bájtos magyar ékezetes sorbarandezési szabályok (csak, ha kizárólag magyar szöveget akarunk)
- **utf8mb4_unicode_ci**: 4 bájtos általános ékezetes sorbarandezési szabályok (**ajánlott**)

Ajánlott adatbázis beállítás:
```sql
CREATE DATABASE `database`
	CHARACTER SET utf8mb4
	COLLATE utf8mb4_unicode_ci;
```

// config/database.php
```php
 'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
```    


3. Az api támogatást le kell telepíteni:
   `php artisan install:api
   `
   A végén kérdezi, hogy letelepítse-e a táblákat: Enter (yes)

!!!! Itt hibaüzenet van.
A hiba oka: [karakterhossz hiba](https://gemini.google.com/share/01a06eea5bdf)

app/Providers/AppServiceProvider.php fájba írjuk be:

```php
//Tegyük be még ezt is a tetejére:
use Illuminate\Support\Facades\Schema;

//Ez marad, e fölé
use Illuminate\Support\ServiceProvider;

//...

//Ebbe a függvénybe:
public function boot(): void
{
    //
    // Az alapértelmezett string hossza 191 karakterre csökkentése
    Schema::defaultStringLength(191);
}
```

-   Migráció újrafuttatása:
    `php artisan migrate:fresh`

vagy
`php artisan migrate:rollback`
`php artisan migrate`

vagy töröjük le az összes táblát az adatbázisban és Futtassuk le a migrációt:
`php artisan migrate`

Létrejönnek a táblák (egy csomó, velük együtt a **user**)

4. Létre kel hozni azt a usert, akivel majd csatlakozni akarunk a seeder-el.
   **database/seeders/DatabaseSeeder.php** fájlban:

```php
public function run(): void
{
    User::factory()->create([
        'name' => 'test',
        'email' => 'test@example.com',
        'password' => '123',
    ]);
}
```

Lefuttatjuk a seeder-t: `php artisan db:seed` és létrejön egy user

5. Az api tesztelése
   routes/api.php

```php
//visszajön az api szöveg
Route::get('/', function(){
    return 'API';
})
```

request.rest fájl létrehozás:

```rest
### változók
@protocol = http://
@hostname = localhost
@port = 8000
@host = {{protocol}}{{hostname}}:{{port}}
### @token = 12|ibiwTdr77o8ppToh1tBxIkckBNb8ECjIjLgaweTaa757cdef

### teszt
get {{host}}/api/


### login
# @name login
POST {{host}}/api/users/login
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "123"
}

###
@token = {{login.response.body.user.token}}

### get users
GET  {{host}}/api/users
Accept: application/json
Authorization: Bearer {{token}}


### get products
GET  {{host}}/api/products

### post products
POST  {{host}}/api/products
Accept: application/json
Content-Type: application/json
Authorization: Bearer {{token}}

{
    "category": "Bogyós",
    "name": "Málna2",
    "description": "Kézzel termelt egészség",
    "picture": "https:\/\/hur.webmania.cc\/img\/malna.jpg",
    "price": 8000,
    "stock": 500
}


### patch products
PATCH  {{host}}/api/products/20
Accept: application/json
Content-Type: application/json
Authorization: Bearer {{token}}

{
    "category": "Bogyósok",
    "price": 4000
}

### delete product by id
DELETE  {{host}}/api/products/21
Accept: application/json
Content-Type: application/json
Authorization: Bearer {{token}}
```

# Egy tábla CRUD

Egy **product** nevű tábla esetén

-   `php artisan make:model product -a --api`
-   Létrehozza a kontrollert az össze metódussal, a modellt és a migrációs fájlt.
    -   migrations\2025_11_01_191501_create_products_table.php
    -   app\Models\product.php
    -   seeders\ProductSeeder.php
    -   app\Http\Controllers\ProductController.php
    -   app\Http\Requests\StoreproductRequest.php
    -   app\Http\Requests\UpdateproductRequest.php
    -   database\factories\ProductFactory.php
    -   app\Policies\ProductPolicy.php

## Migráció

A migráció célja, hogy a táblát (táblákat) létrehozzuk
A migrációs fájlban adjuk meg a tábla definícióját az up() metódusban

migrations\2025_11_01_191501_create_products_table.php

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('category', 255)->notNull();
        $table->string('name', 191)->notNull(); // Ha indexelni akarjuk, ne legyen nagyobb méretű régi mysql motor esetén (mysql: 5.7.7 alatt)
        $table->text('description', 255)->nullable();
        $table->string('picture', 255)->nullable();
        $table->Integer('price')->nullable();
        $table->Integer('stock')->default(1);

        //Minta mezők

        // --- 2. Logikai (BOOLEAN) Alapértelmezett Értékkel
        // Alapértelmezés: FALSE (0)
        // TINYINT(1)
        $table->boolean('is_published')->default(false);

        // --- 3. Dátum (DATE)
        $table->date('start_date');

        // --- 4. Dátum és Idő (DATETIME) Alapértelmezett Értékkel
        // Alapértelmezés: NULL
        $table->dateTime('scheduled_posting')->nullable()->default(null);

        // --- 5. Idő (TIME)
        // Alapértelmezés: '09:00:00'
        $table->time('delivery_time')->default('09:00:00');

        // --- 6. Decimális (DECIMAL) Alapértelmezett Értékkel
        // Ár oszlop: 10 számjegy összesen, 2 tizedesjegy pontossággal.
        // Alapértelmezés: 0.00
        $table->decimal('final_price', 10, 2)->default(0.00);
        // $table->timestamps();
    });
}
```

A Modell-t is nézni kell:
app\Models\product.php

```php
    //...
    //Ne keresd a prodact táblákban a timestamps mezőket
    public $timestamps = false;

    //Ha létrehozunk egy új terméket, akkor milyen adatokat tudunk neki megadni
    //Ez kötelező
    protected $fillable = [
        'category',
        'name',
        'description',
        'picture',
        'price',
        'stock',
        'is_published'
    ];

    //Ezeket kívülről nem tudjuk feltölteni.
    //Nem kötelező
    protected $guarded = [
        'start_date',
        'scheduled_posting',
        'delivery_time',
        'final_price'
    ];

    //Ha az egész tála összes mezőjét visszaküldjük, akkor ezek rejtve maradnak
    //Nem kötlező
    protected $hidden = [
        'final_price'
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'price' => 'integer'
        ];
    }
    //...
```


Hozzuk létre a táblát: `php artisan migrate`

## Egyéb migrációs parancsok:
Az utolsó migráció visszvonása: `php artisan migrate:rollback`
Az utolsó migráció visszvonása: `php artisan migrate:rollback --step=1`
Az utolsó 3 migráció visszvonása: `php artisan migrate:rollback --step=3`
Az összes migráció vissazvonása: `php artisan migrate:reset`
Visszavonja az összes migrációt majd újra lefuttatja őket: `php artisan migrate:refresh`
Visszavonja az összes migrációt majd újra lefuttatja őket és a seedereket: `php artisan migrate:refresh --seed`
Törli az összes táblát és újra migrál (nem fut a down): `php artisan migrate:fresh`
Törli az összes táblát és újra migrál és a seedel: `php artisan migrate:fresh --seed`

Konkrét Migráció Futtatása (up metódus): 
`php artisan migrate --path=database/migrations/2025_01_20_123456_create_products_table.php`

Konkrét Migráció Visszavonása (DOWN metódus)
`php artisan migrate:rollback --path=database/migrations/2025_01_20_123456_create_products_table.php`

Konkrét Migráció Frissítése (Újraépítése) (down(), up())
`php artisan migrate:refresh --path=database/migrations/2025_01_20_123456_create_products_table.php`

## Tábla terv utólagos módosítása (alter)
1. új migrációs módosító fájl létrehozása:
`php artisan make:migration add_unique_index_to_produscts_name_column --table=products`

```php
 public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        // Hozzáadja az 'email' oszlophoz az egyedi indexet
        $table->unique('name', 'products_name_unique');
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        // Eltávolítja az egyedi indexet az 'email' oszlopról
        $table->dropUnique(['name']); 
        // VAGY az index nevével:
        // $table->dropUnique('products_name_unique');
    });
}
```
Megjegyzés: a **Schema::create** helyett itt már a **Schema::table** szerepel.


2. Migráció futtatása: `php artisan migrate`

# Seedelés
A seedelés intézi el a táblák adatokkal való feltöltését.
3 esetet szoktunk:
- sql script
- src fájlból
- teszt feltöltés factory-val véletlen kiválsztással saját tömbből
- Faker könyvtárral

## Tábla seederek
- Minden táblának külön seeder osztályt érdemes készíteni.
Készítsünk egy UserSeeder osztályt (database/seeders/UserSeeder.php):  
`php artisan make:seeder UserSeeder`

database/seeders/UserSeeder.php
```php
public function run(): void
{
    if (User::count()===0) {
        # code...
        User::factory()->create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '123',
        ]);
    }
}
```

Seederek indítása: database/seeders/DatabaseSeeder.php
```php
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

   
    //Előtte érdemes a táblákat letörölni:
    DB::statement('DELETE FROM products');
    DB::statement('DELETE FROM users');


    public function run(): void
    {
        //Ezeket a seedereket futtatja le sorban
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
        ]);

    }
}
```

Seeder futtatása: `php artisan db:seed`
Konkrét seeder osztály futtatása: `php artisan db:seed --class=ProductSeeder`
## Tömbös kézi
Egy tömbbe tesszük az adatokat és ebből generáljuk:

database/seeders/ProductSeeder.php
```php
//Tömbös kézi $data
$data = 
[
    [
        'category' => 'Bogyós',
        'name' => 'Málna',
        'description' => 'Kézzel termelt egészség',
        'picture' => 'https://hur.webmania.cc/img/malna.jpg',
        'price' => 3800,
        'stock' => 500,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Áfonya',
        'description' => 'Az erdő kincse az otthonodba',
        'picture' => 'https://hur.webmania.cc/img/afonya.jpg',
        'price' => 3250,
        'stock' => 120,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Szeder',
        'description' => 'A hagyományos csemege',
        'picture' => 'https://hur.webmania.cc/img/szeder.jpg',
        'price' => 1700,
        'stock' => 40,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Eper',
        'description' => 'Egy tavaszi harapás',
        'picture' => 'https://hur.webmania.cc/img/eper.jpg',
        'price' => 1440,
        'stock' => 0,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Homoktövis',
        'description' => 'Mezei csemege',
        'picture' => 'https://hur.webmania.cc/img/homoktovis.jpg',
        'price' => 3200,
        'stock' => 100,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Som',
        'description' => 'A fanyar gyönyör',
        'picture' => 'https://hur.webmania.cc/img/som.jpg',
        'price' => 900,
        'stock' => 10,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Fanyarka',
        'description' => 'Édes mint a méz',
        'picture' => 'https://hur.webmania.cc/img/fanyarka.jpg',
        'price' => 990,
        'stock' => 25,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Piszke',
        'description' => 'Egres',
        'picture' => 'https://hur.webmania.cc/img/piszke.jpg',
        'price' => 750,
        'stock' => 100,
    ],
    [
        'category' => 'Bogyós',
        'name' => 'Ribizli',
        'description' => 'Fanyar, vasban gazdag',
        'picture' => 'https://hur.webmania.cc/img/ribizli.jpg',
        'price' => 1300,
        'stock' => 170,
    ],
    [
        'category' => 'Magyaros',
        'name' => 'Meggy',
        'description' => 'A falusi kincs',
        'picture' => 'https://hur.webmania.cc/img/meggy.jpg',
        'price' => 600,
        'stock' => 300,
    ],
    [
        'category' => 'Magyaros',
        'name' => 'Cseresznye',
        'description' => 'A falusi kincs',
        'picture' => 'https://hur.webmania.cc/img/cseresznye.jpg',
        'price' => 900,
        'stock' => 300,
    ],
    [
        'category' => 'Magyaros',
        'name' => 'Szilva',
        'description' => 'A falusi kincs',
        'picture' => 'https://hur.webmania.cc/img/szilva.jpg',
        'price' => 770,
        'stock' => 200,
    ],
];

if (Product::count() === 0) {
    Product::factory()->createMany($data);
}

```

## csv fájlból olvassuk fel
database/csv/products.csv
```csv
category;name;description;picture;price;stock
Bogyós;Málna;Kézzel termelt egészség;https://hur.webmania.cc/img/malna.jpg;3800;500
Bogyós;Áfonya;Az erdő kincse az otthonodba;https://hur.webmania.cc/img/afonya.jpg;3250;120
Bogyós;Szeder;A hagyományos csemege;https://hur.webmania.cc/img/szeder.jpg;1700;40
Bogyós;Eper;Egy tavaszi harapás;https://hur.webmania.cc/img/eper.jpg;1440;0
Bogyós;Homoktövis;Mezei csemege;https://hur.webmania.cc/img/homoktovis.jpg;3200;100
Bogyós;Som;A fanyar gyönyör;https://hur.webmania.cc/img/som.jpg;900;10
Bogyós;Fanyarka;Édes mint a méz;https://hur.webmania.cc/img/fanyarka.jpg;990;25
Bogyós;Piszke;Egres;https://hur.webmania.cc/img/piszke.jpg;750;100
Bogyós;Ribizli;Fanyar, vasban gazdag;https://hur.webmania.cc/img/ribizli.jpg;1300;170
Magyaros;Meggy;A falusi kincs;https://hur.webmania.cc/img/meggy.jpg;600;300
Magyaros;Cseresznye;A falusi kincs;https://hur.webmania.cc/img/cseresznye.jpg;900;300
Magyaros;Szilva;A falusi kincs;https://hur.webmania.cc/img/szilva.jpg;770;200
```

database/seeders/ProductSeeder.php
```php
//$data: Felöltés fájlból (database/csv/produts.csv)

// $filePath = database_path(path: 'csv\products.csv');
// $data = [];
// $rows = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
// for ($i = 1; $i < count($rows); $i++) {
//     $cols = explode(';', $rows[$i]);
//     $data[] = [
//         'category' => $cols[0],
//         'name' => $cols[1],
//         'description' => $cols[2],
//         'picture' => $cols[3],
//         'price' => $cols[4],             
//         'stock' => $cols[5]         
//     ];
// }

 //Profibb megoldás (nagyon nagy fájlok esetén):
if (($handle = fopen($filePath, 'r')) !== false) {
    // 1. Beolvassuk a fejléceket (ha vannak)
    $header = fgetcsv($handle, 0, ';');

    // 2. Soronként beolvassuk az adatokat (0 azt jelenti, hogy nincs korlát a beolvasott sorra)
    while (($cols = fgetcsv($handle, 0, ';')) !== false) {
        if (count($header) === count($cols)) {
            // Asszociatív tömb létrehozása (jobb olvashatóság!)
            $data[] = array_combine($header, $cols);
        }
    }
    // 3. Zárjuk a fájlt (itt kötelező!)
    fclose($handle);
}

if (Product::count() === 0) {
    Product::factory()->createMany($data);
}

```


## Faker könyvtár, factory használatával
A lényeg, hogy létezik egy Faker könyvtár, ami a Laravel része.
Ennek segítségével lehet véletlen neveket, számokat, városokat stb. generálni.
[Mire jó a Faker](https://gemini.google.com/share/05a2d76cd792)

Az egész erre egyszerűsödik:
database/seeders/ProductSeeder.php
```php
//100 véletlen termék generálása
Product::factory()->count(100)->create();
```

database/factories/ProductFactory.php
```php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected array $productData = [
        ['category' => 'Bogyós', 'name' => 'Málna', 'description' => 'Kézzel termelt egészség', 'picture' => 'https://hur.webmania.cc/img/malna.jpg'],
        ['category' => 'Bogyós', 'name' => 'Áfonya', 'description' => 'Az erdő kincse az otthonodba', 'picture' => 'https://hur.webmania.cc/img/afonya.jpg'],
        ['category' => 'Bogyós', 'name' => 'Szeder', 'description' => 'A hagyományos csemege', 'picture' => 'https://hur.webmania.cc/img/szeder.jpg'],
        ['category' => 'Bogyós', 'name' => 'Eper', 'description' => 'Egy tavaszi harapás', 'picture' => 'https://hur.webmania.cc/img/eper.jpg'],
        ['category' => 'Bogyós', 'name' => 'Homoktövis', 'description' => 'Mezei csemege', 'picture' => 'https://hur.webmania.cc/img/homoktovis.jpg'],
        ['category' => 'Bogyós', 'name' => 'Som', 'description' => 'A fanyar gyönyör', 'picture' => 'https://hur.webmania.cc/img/som.jpg'],
        ['category' => 'Bogyós', 'name' => 'Fanyarka', 'description' => 'Édes mint a méz', 'picture' => 'https://hur.webmania.cc/img/fanyarka.jpg'],
        ['category' => 'Bogyós', 'name' => 'Piszke', 'description' => 'Egres', 'picture' => 'https://hur.webmania.cc/img/piszke.jpg'],
        ['category' => 'Bogyós', 'name' => 'Ribizli', 'description' => 'Fanyar, vasban gazdag', 'picture' => 'https://hur.webmania.cc/img/ribizli.jpg'],
        ['category' => 'Magyaros', 'name' => 'Meggy', 'description' => 'A falusi kincs', 'picture' => 'https://hur.webmania.cc/img/meggy.jpg'],
        ['category' => 'Magyaros', 'name' => 'Cseresznye', 'description' => 'A falusi kincs', 'picture' => 'https://hur.webmania.cc/img/cseresznye.jpg'],
        ['category' => 'Magyaros', 'name' => 'Szilva', 'description' => 'A falusi kincs', 'picture' => 'https://hur.webmania.cc/img/szilva.jpg'],
    ];


    //A nyelv megadás
    //Ez a függvény a $this->faker első meghívásakor hívódik meg egyszer
    protected function withFaker()
    {
        // Manuális beállítás az app config felülírására
        return \Faker\Factory::create('hu_HU');
    }
    
    //Ez hívódik meg 100-szor és a visszadott asszociatív tömbbel generálja az adatokat
    public function definition(): array
    {
        
        $randomProduct = $this->faker->randomElement($this->productData);
        //A gyümölcs nevét keverjük egy nem ismétlődő véletlen számmal.
        $randomUniqueNumber = $this->faker->unique()->randomNumber(5, true);
        $randomName = $randomProduct['name'].' #'.$randomUniqueNumber;
        // ...
        return [
            'category' => $randomProduct['category'],

            // Ha nem akarsz fix nevet, használhatsz véletlent (ritka a terméknél, de a példa kedvéért)
            // 'name' => $this->faker->lastName() . ' ' . $this->faker->firstName(),
            // 'name' => $this->faker->unique()->name,
            'name' => $randomName,
            'description' => $this->faker->sentence(), //véletlenszerű lorem mondat
            'picture' => $this->faker->imageUrl(),


            // ...
            'price' => $this->faker->numberBetween(500, 5000),
            'stock' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
```

# CRUD

## Endpointok
routes/api.php
```php
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::post('products', [ProductController::class, 'store']);
Route::patch('products/{id}', [ProductController::class, 'update']);
Route::delete('products/{id}', [ProductController::class, 'destroy']);
```

## Kontrollerek
app/Http/Controllers
```php

```

# Cors kezelés
[cors (gemini)](https://g.co/gemini/share/477e6307e707)
A CORS egy olyan biztonsági mechanizmus, amely lehetővé teszi, hogy különböző domain-ekről származó weboldalak egymás erőforrásait hozzáférjék. Ez azért fontos, mert a böngészők alapértelmezett beállítása szerint csak az azonos domainről származó kéréseket engedélyezik. 
Ha ezt nem állítanánk be, a kliens oldal nem férne hozzá az API erőforrásaihoz.
A cors beállítások a http kérésre adott válasz fejlécében érkeznek, és ennek alapján befolyásolja a böngésző működését.

- A cors beállítás létrehozása: `php artisan config:publish cors`
- Létrejön egy **app/config/cors.php** fájl. Itt állíthatjuk be, hogy milyen **metódusokat**, illetve milyen **doménról** szolgálhat ki az API

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // 'allowed_methods' => ['*'],
    'allowed_methods' => ['OPTIONS','GET','POST','PATCH','DELETE'],

    //*: Ez a beállítás engedélyezi a CORS-t bármely domainről érkező kérésekhez.
    //A valóságban itt egy adott (egy-vagy több) domaint engedélyezünk.
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    //Mely fejlécek engedélyezettek a kérésekben
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];    
```

# Helper fájlok
Elhelyezés: app/Helpers mappában.
Használat: A helpers.php fájlban definiált függvényeket bárhonnan meghívhatod az alkalmazásban.
Regisztrálás: A config/app.php fájlban az aliases tömbben kell regisztrálni a helper fájlt.


# Hitelesítés
## Users controller
- Készítünk egy konrollert: `php artisan make:controller UsersController`
**UsersControllers.php**
```php
public function login(LoginUsersRequest $request)
{
    //Eltároljuk az adatokat változókba
    $email = $request->input(('email'));
    $password = $request->input(('password'));

    //Az email alapján megkeressük a usert
    $user = User::where('email', $email)->first();

    //Stimmel-e az email és a jelszó?
    if (!$user || !Hash::check($password, $password ? $user->password : '')) {
        return response()->json([
            'message' => 'invalid email or password'
        ], 401);
    }

    //Jó az email és a jelszó
    //Kitöröljük az esetleges tokenjeit
    //$user->tokens()->delete();

    //itt adjuk az új tokent
    $user->token = $user->createToken('access')->plainTextToken;

    //visszaadjuk a usert, ami a tokent is tartalmazni fogja
    return response()->json([
        'user' => $user
    ]);
}

public function logout(Request $request)
{
    // Megkeresi a tokent és törli ---------------------
    $token = $request->bearerToken(); // Kivonjuk a bearer tokent a kérésből

    // Megkeressük a token modellt
    $personalAccessToken = PersonalAccessToken::findToken($token);

    if ($personalAccessToken) {
        $personalAccessToken->delete();
        return response()->json(['message' => 'Successfully logged out']);
    } else {
        return response()->json(['message' => 'Token not found'], 404);
    }
}

//Visszaadja a usereket
public function index()
{
    $rows = User::all();
    return response()->json(['rows' => $rows], options: JSON_UNESCAPED_UNICODE);
}

public function store(StoreUsersRequest $request)
{
    $row = User::create($request->all());
    return response()->json(['row' => $row], options: JSON_UNESCAPED_UNICODE);
}

public function show(int $id)
{
    $row = User::find($id);

    if ($row) {
        $data = ['row' => $row];
    } else {
        $data = [
            'message' => 'Not found',
            'id' => $id
        ];
    }
    return response()->json($data, options: JSON_UNESCAPED_UNICODE);
}

public function update(UpdateUsersRequest $request,  $id)
{
    $row = User::find($id);
    if ($row) {
        $row->update($request->all());
        $data = ['row' => $row];
    } else {
        $data = [
            'message' => 'Not found',
            'id' => $id
        ];
    }
    return response()->json($data, options: JSON_UNESCAPED_UNICODE);
}

public function destroy(int $id)
{
    $row = User::find($id);
    if ($row) {
        $row->delete();
        $data = [
            'message' => 'Deleted successfully',
            'id' => $id
        ];
    } else {
        $data = [
            'message' => 'Not found',
            'id' => $id
        ];
    }
    return response()->json($data, options: JSON_UNESCAPED_UNICODE);
}
```
## User validátorok

**Requests/LoginUsersRequest.php**
```php
public function rules(): array
{
    return [
        'email' => 'required|email',
        'password' => 'required',
    ];
}
```

**Requests/StoreUsersRequest.php**
```php
public function rules(): array
{
    return [
        'name' => 'required|string',
        'email' => 'required|email',
        'password' => 'required',
    ];
}
```

**Requests/UpdateUsersRequest.php**
```php
public function rules(): array
{
    return [
        'name' => 'nullable|string',
        'email' => 'nullable|email',
        'password' => 'nullable',
    ];
}
```


## Users endpoint 
**route/api.php**
```php 
Route::post('users/login', [UsersController::class, 'login']);
Route::post('users/logout', [UsersController::class, 'logout']);
Route::get('users', [UsersController::class, 'index'])
    ->middleware('auth:sanctum');
Route::get('users/{id}', [UsersController::class, 'show'])
    ->middleware('auth:sanctum');
Route::post('users', [UsersController::class, 'store'])
    ->middleware('auth:sanctum');    
Route::patch('users/{id}', [UsersController::class, 'update'])
    ->middleware('auth:sanctum');    
Route::delete('users/{id}', [UsersController::class, 'destroy'])
    ->middleware('auth:sanctum');
```


## Token élettatam beállítás
**app/config/sanctum.php**
```php
'expiration' => null,
//Token élettartam percben
//'expiration' => 1,

```


