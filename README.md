# MediCore — Cognito + Laravel + PostgreSQL PoC

PoC de autenticación con AWS Cognito y autorización local en PostgreSQL para una app de registros clínicos electrónicos.

## Stack
- Laravel 12 / PHP 8.2
- PostgreSQL (local por defecto, RDS opcional)
- AWS Cognito (User Pool + App Client)
- Docker Compose

## Setup

1. **Preparar entorno:**
   ```bash
   cp .env.example .env
   # (opcional) Generar APP_KEY si no existe:
   docker compose run --rm app php artisan key:generate
   ```

2. **Levantar Docker (incluye PostgreSQL local):**
   ```bash
   docker compose up -d --build
   ```

3. **Instalar dependencias (si no se hizo en el entrypoint):**
   ```bash
   docker compose exec app composer install
   ```

4. **Ejecutar migraciones:**
   ```bash
   docker compose exec app php artisan migrate --force
   ```

5. **Ejecutar seeders:**
   ```bash
   docker compose exec app php artisan db:seed --force
   ```

6. **Acceder:**
   Abrir http://localhost:8080/login

### Usar RDS en producción / desarrollo remoto

Por defecto el stack usa PostgreSQL local dentro de Docker. Para apuntar a una instancia RDS (u otra PostgreSQL remota), edita `.env`:

```env
DB_HOST=tu-rds-host.rds.amazonaws.com
DB_PORT=5432
DB_DATABASE=tu_base
DB_USERNAME=tu_usuario
DB_PASSWORD="tu_password_con_especiales"
DB_SSLMODE=require
```

> **Importante:** si el password contiene `#` u otros caracteres especiales, envuélvelo entre comillas dobles.

Luego reinicia el contenedor:

```bash
docker compose restart app
```

El servicio `db` del `docker-compose.yml` puede seguir levantado o detenerse; la app solo lo usa si `DB_HOST=db`.

## Usuarios de prueba (Cognito)

| Usuario | Contraseña | Rol esperado |
|---------|-----------|-------------|
| ADMIN | AdminTest#123456 | admin |
| ENFERMERA | EnfermeraTest#123456 | enfermera |
| CAJERO | CajeroTest#123456 | cajero |
| INACTIVO | (no existe en Cognito) | cajero (inactivo) |

## Pruebas por rol

- **ADMIN:** puede acceder a Dashboard, Usuarios, Pacientes, Caja.
- **ENFERMERA:** puede acceder a Dashboard y Pacientes. NO Usuarios.
- **CAJERO:** puede acceder a Dashboard y Caja (abrir/cerrar). NO Usuarios.
- **INACTIVO:** aunque exista en Cognito, no existe en Cognito (pero si en local como inactivo). Si se creara en Cognito, el login sería rechazado por "Usuario no está activo".

## Arquitectura

- **Autenticación:** AWS Cognito `InitiateAuth` con `USER_PASSWORD_AUTH`.
- **Sesión:** Laravel native `web` guard con sesiones (`SESSION_DRIVER=file`).
- **Autorización:** Roles y permisos en PostgreSQL (tablas `configuracion.roles`, `configuracion.permissions`, `configuracion.role_has_permissions`).
- **NO JWT validation** en cada request. Solo sesión Laravel.
- **NO Cognito Hosted UI**, NO OAuth callbacks, NO User Migration Lambda.

### Diagrama de secuencia (login)

```mermaid
sequenceDiagram
    actor U as Usuario
    participant B as Navegador
    participant L as Laravel (LoginController)
    participant C as CognitoAuthService
    participant AWS as AWS Cognito
    participant DB as PostgreSQL (RDS)

    U->>B: Ingresa username + password
    B->>L: POST /login
    L->>C: authenticate(username, password)
    C->>AWS: InitiateAuth (USER_PASSWORD_AUTH)
    AWS->>C: AuthenticationResult (IdToken, AccessToken)
    C->>C: Decodificar IdToken (JWT)
    C->>C: Extraer cognito_sub y email
    C->>DB: Buscar User por cognito_sub o email
    DB->>C: User (id, name, role, status, cognito_sub)
    C->>L: Devolver User
    L->>L: Verificar status === 'ACTIVO'
    L->>L: Auth::login(User) → crear sesión Laravel
    L->>B: Redirect /dashboard (Set-Cookie: session_id)
    B->>U: Mostrar Dashboard

    Note over B,DB: Primer login: cognito_sub se guarda en RDS<br/>para resoluciones futuras.

    U->>B: Navega a otra página
    B->>L: GET /patients (Cookie: session_id)
    L->>L: Verificar sesión Laravel (sin JWT)
    L->>DB: Consultar permisos del rol
    DB->>L: Permisos
    L->>L: Verificar User::hasPerm('patients.view')
    L->>B: HTML / Acceso denegado
    B->>U: Mostrar página
```

## Configuración

Las variables de entorno están en `.env`. Claves Cognito:
- `COGNITO_USER_POOL_ID`
- `COGNITO_CLIENT_ID`
- `COGNITO_CLIENT_SECRET`
- `COGNITO_REGION`

> **Nota sobre `COGNITO_ENABLED`:** Aunque no está implementado en el código actual, esta variable de entorno podría usarse como un *feature toggle* para alternar entre autenticación vía Cognito (`true`) y un mecanismo local de desarrollo (`false`, por ejemplo `Auth::attempt` con hashes locales). Esto sería útil para trabajar offline o en entornos sin conectividad a AWS, evitando depender de la red para probar flujos de autorización.
