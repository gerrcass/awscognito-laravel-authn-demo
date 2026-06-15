# PoC: Laravel + AWS Cognito + PostgreSQL (Autenticación Cognito, Autorización Local)

## 1. Contexto

Este proyecto es una demostración aislada (PoC) del patrón arquitectónico de la aplicación real **MediCore**:

- **Autenticación:** AWS Cognito (User Pool + App Client).
- **Autorización:** PostgreSQL + roles/permisos en Laravel (inspirado en `spatie/laravel-permission`).
- **Sesión:** Guard `web` de Laravel con sesiones (NO JWT guard en cada request).
- **UI:** Formulario de login propio en Blade (NO Cognito Hosted UI).
- **Sin** User Migration Lambda.

## 2. Objetivo

Validar el flujo completo:

1. Usuario ingresa credenciales en formulario propio.
2. Laravel valida contra Cognito (`InitiateAuth` con `USER_PASSWORD_AUTH`).
3. Laravel resuelve el `User` local por `cognito_sub` o `email` (username legacy).
4. Laravel crea sesión local (`Auth::login`).
5. Autorización basada en roles/permisos de PostgreSQL.

## 3. Stack Técnico

- **Backend:** Laravel 10+, PHP 8.2
- **Base de datos:** PostgreSQL (RDS remoto en `us-east-2`)
- **Auth externo:** `aws/aws-sdk-php` (Cognito `InitiateAuth`)
- **Auth interno:** Guard `web` + sesiones Laravel (`SESSION_DRIVER=file`)
- **UI:** Blade
- **Contenedor:** Docker Compose (`php:8.2-apache`)
- **NO:** Socialite, `ellaisys/aws-cognito`, JWT guard custom, OAuth callbacks

## 4. Variables de Entorno (.env)

```env
APP_NAME=MediCore-PoC
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=demo-db-1.cds2gwg0q7wo.us-east-2.rds.amazonaws.com
DB_PORT=5432
DB_DATABASE=demo_db
DB_USERNAME=postgres
DB_PASSWORD=Supertest#123456!!!

AWS_REGION=us-east-1
AWS_DEFAULT_REGION=us-east-1

COGNITO_REGION=us-east-1
COGNITO_USER_POOL_ID=us-east-1_yw9xmJJ5a
COGNITO_CLIENT_ID=3p2ktopgdch4ckpm1f0qaharoc
COGNITO_CLIENT_SECRET=1bepfcgk6c9ecqc44jj0g5ahuaj9edfrmubjsj7gftnuthtf4pq4
COGNITO_ENABLED=true

SESSION_DRIVER=file
SESSION_LIFETIME=120
```

## 5. Modelo de Datos (Schema `configuracion`)

### `configuracion.users`
- `id` BIGSERIAL PRIMARY KEY
- `name` VARCHAR(255) NOT NULL
- `email` VARCHAR(255) NOT NULL UNIQUE (username legacy UPPERCASE)
- `correo` VARCHAR(255) NULL (email real opcional)
- `password` VARCHAR(255) NULL (nullable: auth en Cognito)
- `cognito_sub` VARCHAR(255) NULL UNIQUE (UUID Cognito, se llena en primer login)
- `role_user` BIGINT (FK a `configuracion.roles`)
- `status` VARCHAR(10) NOT NULL DEFAULT 'ACTIVO'
- `created_at`, `updated_at` TIMESTAMP

### `configuracion.roles`
- `id` BIGSERIAL PRIMARY KEY
- `name` VARCHAR(255) NOT NULL UNIQUE
- `guard_name` VARCHAR(255) NOT NULL DEFAULT 'web'
- `created_at`, `updated_at` TIMESTAMP

### `configuracion.permissions`
- `id` BIGSERIAL PRIMARY KEY
- `name` VARCHAR(255) NOT NULL UNIQUE
- `guard_name` VARCHAR(255) NOT NULL DEFAULT 'web'
- `created_at`, `updated_at` TIMESTAMP

### `configuracion.role_has_permissions`
- `role_id` BIGINT NOT NULL (FK a `roles`)
- `permission_id` BIGINT NOT NULL (FK a `permissions`)
- PRIMARY KEY (`role_id`, `permission_id`)

## 6. Seeders

### Roles
- `admin`
- `enfermera`
- `cajero`

### Permisos
- `dashboard.view`
- `users.manage`
- `caja.open`
- `caja.close`
- `patients.view`

### Asignación Rol → Permisos
- `admin` → todos
- `enfermera` → `dashboard.view`, `patients.view`
- `cajero` → `dashboard.view`, `caja.open`, `caja.close`

### Usuarios Locales (sin password en RDS)
- `ADMIN` / Administrador / `admin` / `ACTIVO`
- `ENFERMERA` / Enfermera Demo / `enfermera` / `ACTIVO`
- `CAJERO` / Cajero Demo / `cajero` / `ACTIVO`
- `INACTIVO` / Usuario Inactivo / `cajero` / `INACTIVO` (opcional para prueba)

## 7. Patrón de Autorización (Simplificado)

- `User` model: `belongsTo(Role::class, 'role_user')`
- `Role` model: `permissions()` many-to-many
- Helper en `User`:
  ```php
  public function hasPerm(string $name): bool
  {
      return $this->roles?->permissions->contains('name', $name) ?? false;
  }
  ```
- `CheckPermission` middleware: `CheckPermission:permiso.name`
- Vista `acceso-denegado.blade.php` en español

## 8. Flujo de Autenticación (Crítico)

### Guard Laravel
Mantener guard `web` con driver `session` sin cambios en `config/auth.php`. Cognito solo reemplaza la validación de credenciales en el login.

### Login (POST /login)
1. Recibir `email` (username) + `password` desde formulario Blade.
2. Normalizar username a `UPPERCASE`.
3. Llamar `CognitoAuthService::authenticate($username, $password)`:
   ```php
   $cognito->initiateAuth([
       'AuthFlow' => 'USER_PASSWORD_AUTH',
       'ClientId' => config('services.cognito.client_id'),
       'AuthParameters' => [
           'USERNAME' => $username,
           'PASSWORD' => $password,
           'SECRET_HASH' => base64_encode(hash_hmac(
               'sha256',
               $username . config('services.cognito.client_id'),
               config('services.cognito.client_secret'),
               true
           )),
       ],
   ]);
   ```
4. Si éxito, decodificar payload del `IdToken` (JWT, parte central base64).
5. Extraer claims: `sub`, `cognito:username`.
6. Buscar usuario local:
   - Primero: `User::where('cognito_sub', $sub)->first()`
   - Fallback: `User::where('email', $username)->first()`
7. Si no existe local → error "Usuario no registrado en el sistema".
8. Si `status !== 'ACTIVO'` → error "Usuario no está activo".
9. Si `cognito_sub` es NULL → guardar `$sub`.
10. `Auth::login($user)` + `$request->session()->regenerate()`.
11. Redirect `/dashboard`.

### Requests posteriores
- Solo sesión Laravel (`auth` middleware).
- **NO** validar JWT Cognito en cada request.

### Logout (POST /logout)
- `Auth::logout()`
- `$request->session()->invalidate()`
- `$request->session()->regenerateToken()`
- Redirect `/login`

## 9. Rutas y Pantallas

### Públicas
- `GET /login` → formulario (User, Password)
- `POST /login` → `LoginController@login`

### Protegidas (`auth` middleware)
- `GET /dashboard` → requiere `dashboard.view`
- `GET /admin/users` → requiere `users.manage`
- `GET /patients` → requiere `patients.view`
- `GET /caja` → vista con botones abrir/cerrar
- `POST /caja/open` → requiere `caja.open`
- `POST /caja/close` → requiere `caja.close`
- `POST /logout`

Si falta permiso → `acceso-denegado.blade.php` (español).

## 10. Manejo de Errores Cognito (Mensajes en Español)

- `NotAuthorizedException` → "Usuario o contraseña incorrectos"
- `UserNotFoundException` → "Usuario o contraseña incorrectos"
- Usuario local no encontrado → "Usuario no registrado en el sistema"
- `status INACTIVO` → "Usuario no está activo"
- Error AWS/red → "Error de autenticación. Intente nuevamente"

## 11. Estructura de Código Esperada

```
app/
  Http/Controllers/Auth/LoginController.php
  Http/Controllers/DashboardController.php
  Http/Controllers/CajaController.php
  Http/Controllers/Admin/UserController.php
  Http/Controllers/PatientController.php
  Http/Middleware/CheckPermission.php
  Models/User.php
  Models/Role.php
  Models/Permission.php
  Services/Auth/CognitoAuthService.php
config/services.php          # bloque cognito
database/migrations/         # schema configuracion
database/seeders/
resources/views/auth/login.blade.php
resources/views/dashboard.blade.php
resources/views/acceso-denegado.blade.php
resources/views/caja/index.blade.php
resources/views/admin/users/index.blade.php
resources/views/patients/index.blade.php
routes/web.php
docker-compose.yml
README.md
```

## 12. Docker Compose

- Servicio `app` basado en `php:8.2-apache`.
- `DocumentRoot` apuntando a `/var/www/html/public`.
- Volume `.:/var/www/html`.
- Exposición puerto `8080:80`.
- No base de datos local (se conecta a RDS remoto).

## 13. Criterios de Aceptación

- Login `ADMIN` (Cognito) → accede `/dashboard` + `/admin/users`
- Login `ENFERMERA` → accede `/dashboard` + `/patients`; NO `/admin/users`
- Login `CAJERO` → accede `/caja` open/close; NO `/admin/users`
- Usuario `INACTIVO` en RDS → rechazado aunque Cognito autentique
- Tras primer login, `cognito_sub` queda guardado en RDS
- Segundo login resuelve usuario por `cognito_sub`
- Logout destruye sesión Laravel
- NO JWT validation en cada request
- README con instrucciones de setup y prueba por rol

## 14. Restricciones

- NO usar Cognito Hosted UI
- NO usar OAuth callbacks
- NO usar User Migration Lambda
- NO crear JWT custom guard
- NO replicar el monolito completo (solo patrón auth/autz)
- SÍ usar `aws/aws-sdk-php`
- SÍ mantener guard `web` + sesión Laravel

## 15. Contexto de Referencia (Monolito Real)

- Tabla `configuracion.users.email` = username UPPERCASE (ej: `ORODRIGUEZ`), no email real.
- Campo `correo` separado para email real (a menudo NULL).
- Autorización vía `spatie/laravel-permission` customizado (1 rol por usuario).
- Login custom en `LoginController` con `Hash::check` + `Auth::login($user)`.
- AppRunner + ECR + RDS PostgreSQL.

Este PoC valida el puente: **Formulario → Cognito InitiateAuth → resolver User local → Auth::login() → sesión → permisos RDS**.
