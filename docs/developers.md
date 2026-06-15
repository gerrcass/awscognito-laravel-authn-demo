# Guía para Desarrolladores — MediCore PoC

## ¿Qué es este proyecto?

Este es un **Proof of Concept (PoC)** que demuestra cómo integrar **AWS Cognito** para autenticación con **autorización local en PostgreSQL** dentro de una aplicación Laravel. No es una app de producción completa; es un **patrón arquitectónico validado** que se puede replicar en la aplicación real (Total360).

## Objetivo de esta guía

Como desarrollador, necesitas entender los elementos clave para poder replicar este patrón en la aplicación real. Esta guía te explica:

- El flujo de autenticación completo
- El modelo de autorización (roles/permisos)
- Los archivos críticos que debes tocar
- Las decisiones arquitectónicas y por qué se tomaron

---

## Arquitectura de alto nivel

```
Usuario → Formulario Blade → LoginController
                                    ↓
                         CognitoAuthService::authenticate()
                                    ↓
                         AWS Cognito (InitiateAuth)
                                    ↓
                         Decodificar IdToken (JWT payload)
                                    ↓
                         Resolver User local (cognito_sub o email)
                                    ↓
                         Auth::login($user) → Sesión Laravel
                                    ↓
                         Permisos verificados en controllers
```

### Principios clave

1. **Cognito solo valida credenciales en el login.** No reemplaza el guard Laravel.
2. **Después del login, solo existe la sesión Laravel.** No hay validación JWT en cada request.
3. **La autorización es local.** Roles y permisos viven en PostgreSQL, no en Cognito.

---

## Flujo de autenticación detallado

### 1. Login POST `/login`

**Archivo:** `app/Http/Controllers/Auth/LoginController.php`

```php
$username = strtoupper($request->input('email')); // Normalización a UPPERCASE
$password = $request->input('password');

// 1. Llamar a Cognito
$cognito = new CognitoAuthService();
$result = $cognito->authenticate($username, $password);

// 2. Resolver usuario local
$user = User::where('cognito_sub', $sub)->first()
    ?? User::where('email', $username)->first();

// 3. Verificar estado
if ($user->status !== 'ACTIVO') {
    return back()->withErrors(['email' => 'Usuario no está activo']);
}

// 4. Guardar cognito_sub si es primer login
if (empty($user->cognito_sub)) {
    $user->cognito_sub = $sub;
    $user->save();
}

// 5. Login Laravel
Auth::login($user);
$request->session()->regenerate();
```

### 2. Manejo del challenge `NEW_PASSWORD_REQUIRED`

**Archivo:** `app/Services/Auth/CognitoAuthService.php`

Los usuarios en Cognito pueden estar en estado "Force change password". El servicio detecta el challenge `NEW_PASSWORD_REQUIRED` y responde automáticamente con la misma contraseña:

```php
if (isset($result['ChallengeName']) && $result['ChallengeName'] === 'NEW_PASSWORD_REQUIRED') {
    $result = $this->client->respondToAuthChallenge([
        'ChallengeName' => 'NEW_PASSWORD_REQUIRED',
        'ClientId' => $clientId,
        'ChallengeResponses' => [
            'USERNAME' => $username,
            'NEW_PASSWORD' => $password,
            'SECRET_HASH' => $secretHash,
        ],
        'Session' => $result['Session'],
    ]);
}
```

### 3. Logout

**Archivo:** `app/Http/Controllers/Auth/LoginController.php`

```php
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
```

Solo destruye la sesión Laravel. **NO** invalida el token Cognito (por diseño).

---

## Modelo de autorización

### Estructura de tablas

**Schema:** `configuracion`

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios locales. `email` = username UPPERCASE legacy. `cognito_sub` = UUID Cognito. |
| `roles` | Roles: `admin`, `enfermera`, `cajero` |
| `permissions` | Permisos: `dashboard.view`, `users.manage`, `patients.view`, `caja.open`, `caja.close` |
| `role_has_permissions` | Pivot many-to-many entre roles y permisos |

### Relaciones en Eloquent

**Archivo:** `app/Models/User.php`

```php
public function role()
{
    return $this->belongsTo(Role::class, 'role_user');
}

public function hasPerm(string $name): bool
{
    return $this->role?->permissions->contains('name', $name) ?? false;
}
```

**Nota:** `role_user` es una FK a `roles.id`. Un usuario tiene **un solo rol**.

### Verificación de permisos

**Archivo:** `app/Http/Controllers/Admin/UserController.php` (ejemplo)

```php
public function index(Request $request)
{
    \Auth::user()->load('role.permissions');
    if (!\Auth::user()->hasPerm('users.manage')) {
        return response()->view('acceso-denegado', ['permission' => 'users.manage'], 403);
    }
    // ...
}
```

**Decisión:** Los permisos se verifican en los **controllers**, no en middleware. Esto es porque Laravel 12 cambió la forma de registrar middleware y las claves de alias (`'permission'`) no funcionan correctamente con `Route::middleware()`. En la app real, puedes usar middleware si prefieres, pero este PoC usa controller-level checks para máxima compatibilidad.

---

## Archivos críticos para la integración

### Para replicar en la app real:

| Archivo | Responsabilidad |
|---------|-----------------|
| `app/Services/Auth/CognitoAuthService.php` | Wrapper de AWS SDK. Copia este archivo y configura las credenciales en `.env` |
| `app/Http/Controllers/Auth/LoginController.php` | Flujo de login completo. Copia la lógica de `login()` y `logout()` |
| `app/Models/User.php` | Modelo con `hasPerm()` y `belongsTo(Role)`. Adapta a tu schema |
| `config/services.php` | Añade el bloque `cognito` con las claves del App Client |
| `.env` | Añade `COGNITO_*` variables |

### Para adaptar:

- **Tablas:** En la app real, probablemente ya tienes `configuracion.users` con campos similares. Adapta las migraciones.
- **Seeders:** En la app real, los usuarios y roles ya existen. Ajusta `RoleSeeder` y `UserSeeder`.
- **Vistas:** En la app real, usarás tu propio sistema de UI. Las vistas Blade aquí son solo para demostración.

---

## Configuración AWS

### Variables de entorno necesarias

```env
COGNITO_REGION=us-east-1
COGNITO_USER_POOL_ID=us-east-1_yw9xmJJ5a
COGNITO_CLIENT_ID=3p2ktopgdch4ckpm1f0qaharoc
COGNITO_CLIENT_SECRET=1bepfcgk6c9ecqc44jj0g5ahuaj9edfrmubjsj7gftnuthtf4pq4
COGNITO_ENABLED=true
```

### Configuración del App Client en Cognito

- **Authentication flows:** `ALLOW_USER_PASSWORD_AUTH` (Username and password) y `ALLOW_REFRESH_TOKEN_AUTH`
- **NO Hosted UI**
- **NO OAuth callbacks**
- **Sign-in identifier:** Username

---

## Decisiones arquitectónicas y trade-offs

### ¿Por qué NO JWT guard?

- El JWT Cognito expira cada 1 hora. Mantenerlo en cada request requiere refresh tokens y lógica compleja.
- La sesión Laravel es más simple y suficiente para una app web tradicional.
- El JWT solo se usa para **autenticar** en el login, no para **autorizar** en cada request.

### ¿Por qué roles/permisos locales en PostgreSQL?

- Cognito no tiene un sistema de RBAC granular (roles, permisos, múltiples roles por usuario).
- La app real ya usa `spatie/laravel-permission` (o un sistema similar). Mantener la autorización local permite migración gradual.

### ¿Por qué un solo rol por usuario?

- El monolito real usa `role_user` (FK a `roles.id`). No soporta múltiples roles por usuario.
- Para mantener la compatibilidad con la app real, este PoC replica ese patrón.

### ¿Por qué `email` = username UPPERCASE?

- El monolito real usa `configuracion.users.email` como username legacy (ej: `ADMIN`, `ORODRIGUEZ`), no como email real.
- El campo `correo` es el email real (a menudo NULL).
- Este PoC replica ese patrón para mantener la compatibilidad.

---

## Testing local

```bash
# Levantar Docker
docker compose up -d --build

# Instalar dependencias
docker compose exec app composer install

# Migraciones y seeders
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# Acceder
http://localhost:8080/login
```

### Usuarios de prueba

| Usuario | Contraseña | Rol | Permisos |
|---------|-----------|-----|----------|
| ADMIN | AdminTest#123456 | admin | Todos |
| ENFERMERA | EnfermeraTest#123456 | enfermera | dashboard.view, patients.view |
| CAJERO | CajeroTest#123456 | cajero | dashboard.view, caja.open, caja.close |

---

## Notas adicionales

- **Session driver:** `file` (por defecto en Laravel). En producción, considera Redis o database.
- **SSL:** RDS requiere `sslmode=require` en `config/database.php` para PostgreSQL.
- **Cognito users:** En `us-east-1`. RDS en `us-east-2`. Están en regiones diferentes intencionalmente.
- **Docker:** El contenedor usa `php:8.2-apache` con `pdo_pgsql` y `pgsql` extensions.
