# Car Dealers

REST API (Laravel 12) for managing **Manufacturers**, **Dealers** and **Cars**, with full CRUD
for every model and live filtering by any combination of filters for the "All cars" and
"All dealers" pages.

- **Stack:** PHP 8.2, Laravel 12, MySQL 8.0 (Docker) / SQLite (local), nginx
- **Tests:** PHPUnit feature tests on in-memory SQLite

---

## Data model

```
Manufacturer 1 ──< Dealer 1 ──< Car >── 1 Manufacturer
```

- A **manufacturer** has many dealers.
- A **dealer** belongs to one manufacturer and has many cars, which can be of **different manufacturers**.
- A **car** belongs to one dealer and one manufacturer.

| Table | Fields |
|---|---|
| `manufacturers` | `id`, `name` (unique) |
| `dealers` | `id`, `manufacturer_id`, `name`, `city`, `country`, `cars_amount`, `employees_count` |
| `cars` | `id`, `manufacturer_id`, `dealer_id`, `model`, `size`, `color`, `fuel` |

Car attributes are backed by enums in [`app/Enums`](app/Enums):

| Field | Values |
|---|---|
| `size` | `mini`, `compact`, `midsize`, `fullsize`, `suv` |
| `color` | `black`, `white`, `silver`, `gray`, `red`, `blue`, `green`, `yellow` |
| `fuel` | `petrol`, `diesel`, `electric`, `hybrid`, `gas` |

Deleting a manufacturer deletes its dealers and its cars; deleting a dealer deletes its cars.

---

## Getting started

### Option A — Docker (recommended)

Services: `app` (php-fpm + Xdebug), `web` (nginx, port **8080**), `db` (MySQL 8.0, host port **3307**).

```bash
cp .env.example .env
```

Fill in the Docker database credentials in `.env` (they are not stored in `docker-compose.yml`):

```dotenv
DOCKER_DB_DATABASE=car_dealers
DOCKER_DB_USERNAME=car_dealers
DOCKER_DB_PASSWORD=<choose a password>
DOCKER_DB_ROOT_PASSWORD=<choose a password>
```

Then:

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

API: <http://localhost:8080/api>

Inside Docker the app uses MySQL (DB variables are set in `docker-compose.yml` and override `.env`),
so the local setup below keeps working on SQLite.

### Option B — Local PHP

Requires PHP 8.2+ and Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

API: <http://127.0.0.1:8000/api>

### Seed data

`php artisan migrate --seed` creates 6 manufacturers (Toyota, BMW, Volkswagen, Ford, Tesla, Hyundai),
3 dealers per manufacturer and 4–10 cars of random manufacturers per dealer.

---

## API

All endpoints are under `/api` and return JSON. Send `Accept: application/json`.

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/manufacturers` | List (with `dealers_count`, `cars_count`) |
| `POST` | `/manufacturers` | Create |
| `GET` | `/manufacturers/{id}` | Show |
| `PUT` / `PATCH` | `/manufacturers/{id}` | Replace / partially update |
| `DELETE` | `/manufacturers/{id}` | Delete (cascades to its dealers and cars) |
| `GET` | `/dealers` | List with filters |
| `POST` | `/dealers` | Create |
| `GET` | `/dealers/{id}` | Show |
| `PUT` / `PATCH` | `/dealers/{id}` | Replace / partially update |
| `DELETE` | `/dealers/{id}` | Delete (cascades to its cars) |
| `GET` | `/cars` | List with filters |
| `POST` | `/cars` | Create |
| `GET` | `/cars/{id}` | Show |
| `PUT` / `PATCH` | `/cars/{id}` | Replace / partially update |
| `DELETE` | `/cars/{id}` | Delete |
| `GET` | `/filters/cars` | Available values for the "All cars" filter panel |
| `GET` | `/filters/dealers` | Available values for the "All dealers" filter panel |

**PUT vs PATCH:** `PUT` is a full replacement — all fields are required (same rules as create).
`PATCH` is a partial update — send only the fields you want to change.

### Filtering

Every filter is optional and they can be combined freely:

- several values of **one** filter are combined with **OR** — `fuel[]=electric&fuel[]=hybrid`
- **different** filters are combined with **AND** — `fuel[]=electric&color[]=red`

Multi-value filters must be sent as arrays (`fuel[]=electric`, not `fuel=electric`).

**`GET /api/cars`**

| Param | Type | Example |
|---|---|---|
| `manufacturer_id[]` | int[] | `manufacturer_id[]=1&manufacturer_id[]=5` |
| `dealer_id[]` | int[] | `dealer_id[]=3` |
| `size[]` | enum[] | `size[]=suv` |
| `color[]` | enum[] | `color[]=red&color[]=black` |
| `fuel[]` | enum[] | `fuel[]=electric` |
| `search` | string | `search=Corolla` (by model) |
| `sort` | `id`, `model`, `size`, `color`, `fuel`, `created_at` | `sort=model` |
| `direction` | `asc`, `desc` | `direction=asc` |
| `per_page` | 1–100 (default 15) | `per_page=50` |

**`GET /api/dealers`**

| Param | Type | Example |
|---|---|---|
| `manufacturer_id[]` | int[] | `manufacturer_id[]=2` |
| `country[]` | string[] | `country[]=Germany` |
| `city[]` | string[] | `city[]=Berlin` |
| `cars_amount_min` / `cars_amount_max` | int | `cars_amount_min=5` |
| `employees_min` / `employees_max` | int | `employees_min=10&employees_max=50` |
| `search` | string | `search=Motors` (by name) |
| `sort` | `id`, `name`, `city`, `country`, `cars_amount`, `employees_count`, `created_at` | `sort=employees_count` |
| `direction` | `asc`, `desc` | `direction=desc` |
| `per_page` | 1–100 (default 15) | `per_page=50` |

Lists are paginated (`data`, `links`, `meta`). Invalid filter values return `422`.

### Examples

```bash
# Electric or hybrid SUVs
curl -H "Accept: application/json" \
  "http://localhost:8080/api/cars?fuel[]=electric&fuel[]=hybrid&size[]=suv"

# Dealers in Germany with at least 20 employees, biggest first
curl -H "Accept: application/json" \
  "http://localhost:8080/api/dealers?country[]=Germany&employees_min=20&sort=employees_count&direction=desc"

# Create a car
curl -X POST -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"manufacturer_id":1,"dealer_id":1,"model":"Corolla","size":"compact","color":"red","fuel":"hybrid"}' \
  http://localhost:8080/api/cars
```

### CORS

Allowed origins are set by `CORS_ALLOWED_ORIGINS` (comma-separated), default
`http://localhost:5173,http://127.0.0.1:5173` (Vite dev server).

---

## Design decisions

### `cars_amount` is maintained automatically

The task lists "amount of cars" as a Dealer field but does not say whether it is entered manually
or derived. It is implemented as a stored column that always matches the dealer's real number of cars:

- it is **read-only via the API** — ignored if sent in `POST`/`PUT`/`PATCH /dealers`;
- [`CarObserver`](app/Observers/CarObserver.php) recalculates it when a car is created, deleted or
  moved to another dealer, using [`SyncDealerCarsAmountAction`](app/Actions/Dealer/SyncDealerCarsAmountAction.php);
- deleting a manufacturer removes its cars at other dealers through a DB cascade, which bypasses model
  events, so [`DeleteManufacturerAction`](app/Actions/Manufacturer/DeleteManufacturerAction.php)
  re-syncs the affected dealers explicitly.

If it should be a manually entered value instead, add it to `StoreDealerRequest` and
`Dealer::$fillable`, and remove the observer.

### Thin controllers, logic in Actions

Controllers only handle HTTP: validated request → action → API resource. Business logic lives in
single-purpose classes in [`app/Actions`](app/Actions) (`List…`, `Create…`, `Update…`, `Delete…`,
filter options), which take plain arrays/models and know nothing about HTTP.

```
app/
├── Actions/          Car/, Dealer/, Manufacturer/, FilterOptions/
├── Enums/            CarSize, CarColor, CarFuel
├── Http/
│   ├── Controllers/Api/
│   ├── Requests/     Store*/Update*/Index* form requests
│   └── Resources/    JSON resources
├── Models/           Manufacturer, Dealer, Car (filter scopes)
└── Observers/        CarObserver
```

Filtering lives in `scopeFilter()` on the [`Car`](app/Models/Car.php) and
[`Dealer`](app/Models/Dealer.php) models; allowed sort columns are whitelisted in the index requests.

---

## Tests

```bash
php artisan test
# or in Docker
docker compose exec app php artisan test
```

Feature tests cover CRUD, validation, every filter alone and combined, sorting, `PUT` vs `PATCH`,
`cars_amount` syncing (create / move / delete / manufacturer cascade) and filter options.

Tests always run on in-memory SQLite (forced in `phpunit.xml`), so running them inside Docker never
touches the MySQL data.
