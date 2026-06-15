# Guía para Usuarios — MediCore PoC

## ¿Qué es esta aplicación?

Esta es una **demostración (PoC)** de un sistema de registros clínicos electrónicos que valida cómo los usuarios pueden iniciar sesión usando **AWS Cognito** y acceder a diferentes secciones según su rol.

## Cómo acceder

1. Abre tu navegador y ve a: **http://localhost:8080/login**
2. Ingresa tu **Usuario** y **Contraseña** (ver tabla de usuarios de prueba abajo)
3. Haz clic en **Ingresar**

## Usuarios de prueba

| Usuario | Contraseña | Rol | ¿Qué puede hacer? |
|---------|-----------|-----|-------------------|
| ADMIN | AdminTest#123456 | Administrador | Ver todo: Dashboard, Pacientes, Usuarios, Caja |
| ENFERMERA | EnfermeraTest#123456 | Enfermera | Ver Dashboard, Pacientes. NO puede ver Usuarios ni operar Caja |
| CAJERO | CajeroTest#123456 | Cajero | Ver Dashboard, Caja (abrir/cerrar). NO puede ver Usuarios ni Pacientes |
| INACTIVO | (no existe en Cognito) | Cajero inactivo | Si se creara en Cognito, el login sería rechazado con "Usuario no está activo" |

## Flujos que puedes completar

### 1. Iniciar sesión como ADMIN
- **Paso 1:** Ve a `/login`, ingresa `ADMIN` / `AdminTest#123456`
- **Paso 2:** Verás el **Dashboard** con mensaje "Has iniciado sesión correctamente"
- **Paso 3:** En la barra de navegación verás: **Dashboard**, **Usuarios**, **Pacientes**, **Caja**, **Cerrar sesión**
- **Paso 4:** Haz clic en **Usuarios** → verás una tabla con todos los usuarios del sistema
- **Paso 5:** Haz clic en **Pacientes** → verás una lista de pacientes de ejemplo
- **Paso 6:** Haz clic en **Caja** → verás botones para **Abrir caja** y **Cerrar caja**
- **Paso 7:** Haz clic en **Cerrar sesión** → regresarás al login

### 2. Iniciar sesión como ENFERMERA
- **Paso 1:** Ve a `/login`, ingresa `ENFERMERA` / `EnfermeraTest#123456`
- **Paso 2:** Verás el **Dashboard**
- **Paso 3:** En la barra de navegación verás: **Dashboard**, **Pacientes**, **Cerrar sesión**
- **Paso 4:** NOTA: **NO** verás **Usuarios** ni **Caja** (no tiene permisos)
- **Paso 5:** Haz clic en **Pacientes** → verás la lista de pacientes
- **Paso 6:** Si intentas ir manualmente a `/admin/users` → verás **"Acceso denegado"**
- **Paso 7:** Cierra sesión cuando termines

### 3. Iniciar sesión como CAJERO
- **Paso 1:** Ve a `/login`, ingresa `CAJERO` / `CajeroTest#123456`
- **Paso 2:** Verás el **Dashboard**
- **Paso 3:** En la barra de navegación verás: **Dashboard**, **Caja**, **Cerrar sesión**
- **Paso 4:** NOTA: **NO** verás **Usuarios** ni **Pacientes**
- **Paso 5:** Haz clic en **Caja** → verás botones para **Abrir caja** y **Cerrar caja**
- **Paso 6:** Haz clic en **Abrir caja** → verás mensaje "Caja abierta correctamente"
- **Paso 7:** Haz clic en **Cerrar caja** → verás mensaje "Caja cerrada correctamente"
- **Paso 8:** Si intentas ir manualmente a `/admin/users` → verás **"Acceso denegado"**
- **Paso 9:** Cierra sesión cuando termines

### 4. Probar usuario INACTIVO
- **Paso 1:** El usuario `INACTIVO` existe en la base de datos local pero **no existe en Cognito**.
- **Paso 2:** Si intentas ingresar `INACTIVO` / `CajeroTest#123456` → verás **"Usuario o contraseña incorrectos"** (porque Cognito no reconoce al usuario).
- **Paso 3:** Para probar el estado INACTIVO, primero tendrías que crear el usuario `INACTIVO` en Cognito Console con password `CajeroTest#123456`, luego intentar login → verías **"Usuario no está activo"**.
- **Paso 4:** El usuario `INACTIVO` en la base de datos local tiene `status = 'INACTIVO'` y rol `cajero`. Sirve para demostrar que incluso si Cognito autentica, el sistema local puede rechazar al usuario por su estado.

### 5. Verificar persistencia de cognito_sub
- **Paso 1:** Inicia sesión como cualquier usuario (ej: ADMIN)
- **Paso 2:** Ve a **Usuarios** (solo disponible para ADMIN)
- **Paso 3:** Verás la columna **Cognito Sub** que muestra el UUID vinculado al usuario de Cognito
- **Paso 4:** Este UUID viene del **IdToken JWT** que Cognito devuelve en el login. Se guarda automáticamente en la base de datos local (`configuracion.users.cognito_sub`) en la primera sesión exitosa.
- **Paso 5:** En **nuevos intentos de login** (posteriores), el sistema busca primero por `cognito_sub` en la BD local. Si encuentra el usuario, evita buscar por `email` (username), haciendo la resolución más directa.

### 6. Probar logout
- **Paso 1:** Inicia sesión como cualquier usuario
- **Paso 2:** Haz clic en **Cerrar sesión**
- **Paso 3:** Serás redirigido al login
- **Paso 4:** Si intentas ir manualmente a `/dashboard` → serás redirigido al login automáticamente
- **Paso 5:** La sesión Laravel se destruye completamente (NO queda token JWT)

## Mensajes de error que puedes ver

| Mensaje | Significado |
|---------|-------------|
| "Usuario o contraseña incorrectos" | Las credenciales no coinciden con Cognito |
| "Usuario no registrado en el sistema" | El usuario existe en Cognito pero NO en la base de datos local |
| "Usuario no está activo" | El usuario existe en la base de datos local pero está marcado como INACTIVO |
| "Error de autenticación. Intente nuevamente" | Error de red o de AWS |
| "Acceso denegado" | El usuario no tiene permiso para ver esa sección |

## ¿Qué NO puedes hacer en esta demo?

- ❌ Crear nuevos usuarios (no hay registro)
- ❌ Cambiar contraseñas (Cognito maneja las contraseñas)
- ❌ Eliminar pacientes (son datos de demo estáticos)
- ❌ Modificar roles o permisos (requeriría acceso a la base de datos)
- ❌ Ver la UI de Cognito Hosted (no está habilitada)

## ¿Qué SÍ puedes hacer?

- ✅ Iniciar/cerrar sesión con diferentes roles
- ✅ Ver cómo los permisos restringen el acceso a secciones
- ✅ Ver la lista de pacientes (datos de demo)
- ✅ Ver la lista de usuarios (como ADMIN)
- ✅ Ver cómo se vincula el usuario de Cognito con el usuario local (columna `cognito_sub`)
- ✅ Ver cómo el sistema usa sesiones Laravel (no JWT en cada request)

## Preguntas frecuentes

**Q: ¿Por qué no puedo ver la sección de Usuarios?**
A: Solo los usuarios con rol `admin` tienen permiso `users.manage`. Los roles `enfermera` y `cajero` no tienen este permiso.

**Q: ¿Qué pasa si intento ir a una URL sin iniciar sesión?**
A: Serás redirigido automáticamente a `/login`. El sistema usa el guard `web` de Laravel con sesiones.

**Q: ¿Por qué el usuario se llama ENFERMERA y no un nombre real?**
A: En el sistema real, `configuracion.users.email` se usa como username legacy (UPPERCASE), no como email real. El campo `correo` es el email real.

**Q: ¿Qué es el `cognito_sub` que veo en la tabla de usuarios?**
A: Es el UUID único del usuario en AWS Cognito. Se guarda en la primera sesión exitosa para identificar al usuario rápidamente en sesiones posteriores.

**Q: ¿Por qué no veo un token JWT en cada request?**
A: Este PoC usa sesiones Laravel nativas (`SESSION_DRIVER=file`). El JWT de Cognito (IdToken) solo se usa durante el login para validar credenciales y extraer el `sub` (UUID del usuario). Después del login, solo existe la sesión Laravel.

**Q: ¿Dónde está el JWT de Cognito? ¿Puedo verlo en el navegador?**
A: No. El JWT de Cognito (IdToken) solo existe en memoria del servidor durante el login (`CognitoAuthService::authenticate()`). El servidor lo recibe, decodifica el payload para obtener el `sub`, y luego lo descarta. No se envía al navegador. El cookie que ves (`medicore-poc-session`) es la **sesión de Laravel encriptada** — es completamente diferente del JWT de Cognito. Para ver el JWT de Cognito, tendrías que añadir un `dd($result)` o `Log::info()` en `CognitoAuthService::authenticate()` después de la llamada a `initiateAuth()`.

**Q: ¿Por qué la sesión de Laravel se encripta?**
A: Laravel encripta las cookies de sesión por seguridad (configurable en `config/session.php`). El valor que ves en DevTools (`eyJpdiI6...`) no es el JWT de Cognito; es la sesión de Laravel serializada y encriptada. Contiene el `user_id` del usuario autenticado, el CSRF token, y otros datos de sesión.

---

Para más información técnica, consulta la guía para desarrolladores en `docs/developers.md`.
