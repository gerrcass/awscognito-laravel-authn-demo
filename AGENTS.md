# AGENTS.md

## Project Overview

**Name:** MediCore-PoC  
**Description:** Proof of concept (PoC) for a clinical records application demonstrating AWS Cognito authentication with local PostgreSQL authorization in Laravel.  
**Purpose:** Validate the architectural pattern for authentication (Cognito) and authorization (local roles/permissions) before integrating into the real application.

## Architecture

- **Backend:** Laravel 12 (PHP 8.2)
- **Database:** PostgreSQL (RDS remote) — schema `configuracion`
- **Authentication:** AWS Cognito (User Pool + App Client) via `InitiateAuth` with `USER_PASSWORD_AUTH`
- **Session:** Laravel native `web` guard with file-based sessions (NO JWT per-request validation)
- **Authorization:** Custom roles/permissions system (inspired by spatie/laravel-permission) using `configuracion.roles`, `configuracion.permissions`, `configuracion.role_has_permissions`
- **UI:** Blade templates (NO Cognito Hosted UI)
- **Container:** Docker Compose with `php:8.2-apache`

## Key Patterns for Real App Integration

1. **Authentication Flow:**
   - Formulario → `CognitoAuthService::authenticate()` → `InitiateAuth` → decodificar `IdToken` → resolver `User` local por `cognito_sub` o `email` → `Auth::login()` → sesión Laravel
   - Tras primer login: `cognito_sub` se guarda en RDS para resolución futura
   - Cognito NO reemplaza el guard Laravel; solo reemplaza la validación de credenciales en login

2. **Authorization Model:**
   - `User` → `belongsTo(Role, role_user)` (un solo rol por usuario)
   - `Role` → `hasMany(Permission)` (many-to-many vía pivot)
   - Helper `User::hasPerm(string $name)` para verificar permisos
   - Permisos verificados en controllers (no middleware, por compatibilidad con Laravel 12)

3. **Critical Files:**
   - `app/Services/Auth/CognitoAuthService.php` — wrapper de AWS SDK
   - `app/Http/Controllers/Auth/LoginController.php` — flujo de login
   - `app/Models/User.php` — modelo con `hasPerm()`
   - `app/Http/Controllers/` — controllers verifican permisos directamente

4. **Environment Variables:**
   - `.env` contiene credenciales RDS y Cognito (NO commiteado)
   - `config/services.php` expone bloque `cognito` con `client_id`, `client_secret`, `user_pool_id`, `region`

5. **Docker:**
   - `Dockerfile` — `php:8.2-apache` con extensiones pgsql
   - `docker-compose.yml` — volume mount, puerto 8080:80
   - `composer install` y `apache2-foreground` en startup

## Constraints

- NO Cognito Hosted UI
- NO OAuth callbacks
- NO User Migration Lambda
- NO JWT custom guard
- NO replicar el monolito completo (solo patrón auth/autz)
- SÍ usar `aws/aws-sdk-php`
- SÍ mantener guard `web` + sesión Laravel

## Development Commands

```bash
# Levantar Docker
docker compose up -d --build

# Instalar dependencias
docker compose exec app composer install

# Ejecutar migraciones
docker compose exec app php artisan migrate --force

# Ejecutar seeders
docker compose exec app php artisan db:seed --force

# Ver logs
docker compose logs app -f

# Acceder a la app
http://localhost:8080/login
```

## Test Users (Cognito)

| Usuario | Contraseña | Rol local | Permisos |
|---------|-----------|-----------|----------|
| ADMIN | `AdminTest#123456` | admin | Todos |
| ENFERMERA | `EnfermeraTest#123456` | enfermera | dashboard.view, patients.view |
| CAJERO | `CajeroTest#123456` | cajero | dashboard.view, caja.open, caja.close |

## Notes for Agents

- When working on this project, always check the current `.env` for credentials (they are NOT in git)
- The RDS security group must allow inbound TCP 5432 from the current IP
- Cognito users are in `us-east-1`, RDS in `us-east-2`
- Session driver is `file` (stored in `storage/framework/sessions/`)
- The `cognito_sub` field is populated on first successful login and used for subsequent lookups
