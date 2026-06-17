# Guía para Desarrolladores — MediCore PoC

## ¿Qué es este proyecto?

Este es un **Proof of Concept (PoC)** que demuestra cómo integrar **AWS Cognito** para autenticación con **autorización local en PostgreSQL** dentro de una aplicación Laravel. No es una app de producción completa; es un **patrón arquitectónico validado** que se puede replicar en la aplicación real.

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

Los usuarios en Cognito pueden estar en estado "Force change password". El servicio detecta el challenge `NEW_PASSWORD_REQUIRED` y responde automáticamente con la misma contraseña (workaround para este PoC):

> **Nota para producción:** En la app real, si el usuario está en estado `Force change password`, deberías redirigirlo a una pantalla de cambio de contraseña donde el usuario ingrese su nueva contraseña, y luego llamar a `respondToAuthChallenge` con `NEW_PASSWORD` proporcionada por el usuario. El workaround aquí (usar la misma contraseña temporal) es solo para facilitar las pruebas del PoC.

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

> **¿Por qué no invalidamos el token Cognito?** El token de Cognito (AccessToken, IdToken) es de corta duración (1 hora por defecto) y es emitido por Cognito. Para invalidarlo de forma remota habría que usar `GlobalSignOut` o `AdminUserGlobalSignOut`, lo cual añade complejidad y latencia. En una app web tradicional con sesiones Laravel, la sesión es el mecanismo de control de acceso; el token Cognito solo se usa durante el login. Invalidar la sesión Laravel es suficiente para bloquear al usuario. Además, el RefreshToken (válido por 30 días) persistiría de todos modos, lo que requeriría lógica adicional de revocación que escapa al alcance de este patrón.

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

**Decisión:** Los permisos se verifican en los **controllers**, no en middleware. Esto es porque este PoC usa Laravel 12, donde el registro de middleware aliases (`'permission'`) en `bootstrap/app.php` no funciona correctamente con `Route::middleware()` en este contexto. 

> **Para la app real (Laravel 10):** Tu app usa `"laravel/framework": "^10.0"`, donde sí tienes `app/Http/Kernel.php` con `$routeMiddleware`. Allí puedes registrar `'permission' => \App\Http\Middleware\CheckPermission::class` y usar `Route::middleware('permission:users.manage')` directamente en las rutas. Las vistas blade aquí usan `hasPerm()` directamente; en la app real puedes usar middleware si lo prefieres. Este PoC usa controller-level checks para máxima compatibilidad.

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

- **Tablas:** En la app real, el DBA gestiona el schema directamente (no se usan migraciones de Laravel). Los cambios en `configuracion.users` (añadir `cognito_sub`, etc.) se hacen directamente en la base de datos. Adapta los modelos Eloquent (`$table`, `$fillable`) para reflejar los campos existentes.
- **Seeders:** En la app real, los usuarios y roles ya existen. Ajusta `RoleSeeder` y `UserSeeder`.
- **Vistas:** En la app real, usarás tu propio sistema de UI. Las vistas Blade aquí son solo para demostración.

---

## ¿Por qué `aws/aws-sdk-php` y no Socialite o `ellaisys/aws-cognito`?

| Opción | ¿Por qué no se usó? |
|--------|---------------------|
| **Laravel Socialite** | Diseñado para OAuth 2.0 (Google, GitHub, etc.). Cognito con `InitiateAuth` (flujo directo username/password) no es OAuth. Socialite no soporta `USER_PASSWORD_AUTH`. |
| **ellaisys/aws-cognito** | Usa OAuth Authorization Code flow con Cognito Hosted UI. Este PoC explícitamente **NO** usa Hosted UI ni OAuth callbacks. Además, la librería introduce abstracciones innecesarias para este patrón simple. |
| **aws/aws-sdk-php** | Librería oficial de AWS. Da control total sobre `CognitoIdentityProviderClient::initiateAuth()`. Permite manejar challenges (`NEW_PASSWORD_REQUIRED`), SECRET_HASH, y decodificar el IdToken directamente. Es la opción más directa y transparente. |

> **Conclusión:** Se eligió `aws/aws-sdk-php` porque este patrón usa Cognito como **servicio de validación de credenciales** (reemplazando `Hash::check()`) dentro de un login custom Laravel, no como proveedor OAuth. Necesitamos control directo sobre `InitiateAuth`, no abstracciones de OAuth.

---

## Configuración AWS

### Variables de entorno necesarias

```env
COGNITO_REGION=us-east-1
COGNITO_USER_POOL_ID=us-east-1_yw9xmJJ5a
COGNITO_CLIENT_ID=3p2ktopgdch4ckpm1f0qaharoc
COGNITO_CLIENT_SECRET=1bepfcgk6c9ecqc44jj0g5ahuaj9edfrmubjsj7gftnuthtf4pq4
```

> **Nota sobre `COGNITO_ENABLED`:** No está implementado en el código actual, pero podría usarse como un *feature toggle* para alternar entre autenticación Cognito (`true`) y un mecanismo local de desarrollo (`false`, por ejemplo `Auth::attempt` con hashes locales). Útil para trabajar offline sin conectividad a AWS.

### Configuración del App Client en Cognito

- **Authentication flows:** `ALLOW_USER_PASSWORD_AUTH` (Username and password) y `ALLOW_REFRESH_TOKEN_AUTH`
- **NO Hosted UI**
- **NO OAuth callbacks**
- **Sign-in identifier:** Username (elegido como ejemplo para este PoC; ajustable según la configuración de tu User Pool)

---

## Decisiones arquitectónicas y trade-offs

### ¿Por qué NO JWT guard?

- El JWT Cognito expira cada 1 hora. Mantenerlo en cada request requiere refresh tokens y lógica compleja.
- La sesión Laravel es más simple y suficiente para una app web tradicional.
- El JWT solo se usa para **autenticar (authN)** en el login, no para **autorizar (authZ)** en cada request.

### ¿Por qué roles/permisos locales en PostgreSQL?

- La app real ya tiene todo el mecanismo de autorización (roles, permisos) implementado en Laravel. No se quiere migrar a un sistema RBAC externo.
- Mantener la autorización local permite reutilizar el sistema existente sin cambios significativos.

### ¿Por qué un solo rol por usuario?

- El monolito real usa `role_user` (FK a `roles.id`). No soporta múltiples roles por usuario.
- Para mantener la compatibilidad con la app real, este PoC replica ese patrón.

### ¿Por qué `email` = username UPPERCASE?

- El monolito real usa `configuracion.users.email` como username legacy (ej: `ADMIN`, `ORODRIGUEZ`), no como email real.
- El campo `correo` es el email real (a menudo NULL).
- Este PoC replica ese patrón para mantener la compatibilidad.

---

## Testing local

El stack usa **PostgreSQL local dentro de Docker** por defecto. No necesitas credenciales RDS ni conectividad a AWS para probar el flujo de autorización local.

```bash
# 1. Preparar entorno
cp .env.example .env
# (si no tienes APP_KEY, generarlo: docker compose run --rm app php artisan key:generate)

# 2. Levantar Docker (app + PostgreSQL local)
docker compose up -d --build

# 3. Instalar dependencias (si no se hizo en entrypoint)
docker compose exec app composer install

# 4. Migraciones y seeders
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# 5. Acceder
http://localhost:8080/login
```

### Usar RDS en lugar de PostgreSQL local

Para apuntar a RDS (o cualquier PostgreSQL remoto), edita `.env`:

```env
DB_HOST=tu-rds-host.rds.amazonaws.com
DB_PORT=5432
DB_DATABASE=tu_base
DB_USERNAME=tu_usuario
DB_PASSWORD="tu_password"
DB_SSLMODE=require
```

Reinicia el contenedor:

```bash
docker compose restart app
```

> El servicio `db` del `docker-compose.yml` puede seguir levantado o detenerse; la app solo lo usa si `DB_HOST=db`.

### Usuarios de prueba

| Usuario | Contraseña | Rol | Permisos |
|---------|-----------|-----|----------|
| ADMIN | AdminTest#123456 | admin | Todos |
| ENFERMERA | EnfermeraTest#123456 | enfermera | dashboard.view, patients.view |
| CAJERO | CajeroTest#123456 | cajero | dashboard.view, caja.open, caja.close |

---

## Notas adicionales

- **Session driver:** `file` (por defecto en Laravel). En producción, considera Redis o database.
- **SSL:** `config/database.php` usa `sslmode=prefer` por defecto. Esto intenta SSL cuando está disponible (RDS) pero no falla en conexiones locales sin SSL. Para RDS explícito, usa `DB_SSLMODE=require` en `.env`.
- **Cognito users:** En `us-east-1`. RDS en `us-east-2`. Están en regiones diferentes intencionalmente.
- **Docker:** El contenedor usa `php:8.2-apache` con `pdo_pgsql` y `pgsql` extensions.
