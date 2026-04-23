# NEXORA CONSULTING GROUP - Sistema de Control de Presencia

Aplicación web de control de presencia desarrollada 100% con tecnologías nativas: **HTML5, CSS3, JavaScript Vanilla, PHP y MySQL**. 

---

## ✅ Características Principales

✅ **Detección automática de VPN corporativa** para validar fichajes
✅ **Soporte modalidades**: Presencial, Híbrido, Teletrabajo
✅ **Desfichaje nocturno manual** con comentario obligatorio y marca para revisión RRHH
✅ **Sistema de roles**: Empleado, Jefe Departamento, Administrador / RRHH
✅ **Gestión de horas extras** con flujo de aprobación
✅ **Horarios personalizados** por empleado y días de teletrabajo
✅ **Gráficos nativos Canvas API** sin librerías externas
✅ **Exportación CSV y PDF**
✅ **Sistema de notificaciones** y alertas
✅ **Directorio de empleados** filtrable por departamento
✅ **Diseño responsive premium corporativo**
✅ **Instalador automático** paso a paso

---

## 🛠️ Requisitos Técnicos

- PHP 7.4+
- MySQL 5.7+
- Extensiones PHP: PDO, pdo_mysql, gd
- Servidor web Apache / Nginx

---

## 🚀 Instalación Automática

1.  Accede a `/install.php` desde tu navegador
2.  Introduce tus credenciales de MySQL
3.  Pulsa "Iniciar Instalación Completa"

El instalador realizará automáticamente:
✅ Verificación de requisitos del sistema
✅ Creación de la base de datos `nexora_consulting`
✅ Importación de toda la estructura y datos de prueba
✅ Creación de directorios y permisos
✅ Generación automática del archivo `.htaccess`
✅ Actualización automática de credenciales en `config.php`
✅ Bloqueo de re-instalación accidental

---

## 🔑 Credenciales de Acceso de Prueba

> ✅ **TODAS estas credenciales están ya creadas y listas para usar inmediatamente después de la instalación:**

| Rol | Email | Contraseña | Permisos |
|---|---|---|---|
| 👤 **Empleado** | `marc.puig@nexora.com` | `Nexora2025!` | Acceso completo a módulo empleado |
| 👤 **Empleado** | `laura.gomez@nexora.com` | `Nexora2025!` | Departamento Contabilidad |
| 👤 **Empleado** | `sofia.martin@nexora.com` | `Nexora2025!` | Departamento RRHH |
| 👤 **Empleado** | `andreu.sala@nexora.com` | `Nexora2025!` | Departamento Dirección |
| 👔 **Jefe Departamento** | `elena.torres@nexora.com` | `NexoraJefe2025!` | Desarrollo |
| 👔 **Jefe Departamento** | `victor.ruiz@nexora.com` | `NexoraJefe2025!` | Contabilidad |
| 👔 **Jefe Departamento** | `nuria.costa@nexora.com` | `NexoraJefe2025!` | RRHH |
| 👔 **Jefe Departamento** | `jordi.font@nexora.com` | `NexoraJefe2025!` | Dirección |
| 🔐 **Administrador Global** | `admin@nexora.com` | `NexoraJefe2025!` | Acceso completo a TODA la empresa |

> 💡 **Recomendación**: Usa `marc.puig@nexora.com` para probar la experiencia como empleado normal y `admin@nexora.com` para ver todas las funcionalidades de supervisor.

---

## 📋 Módulos de la Aplicación

### 🔹 Módulo Empleado (9 pantallas)
✅ **Inicio**: Resumen diario, horas trabajadas, estado actual
✅ **Fichar**: Botón entrada/salida, indicador VPN, historial, registro manual nocturno
✅ **Mi Perfil**: Datos personales, modalidad, foto editable
✅ **Horario**: Horario semanal, días teletrabajo, festivos
✅ **Mis Proyectos**: Proyectos asignados, horas por proyecto, registro de tareas
✅ **Horas Extras**: Solicitud, estado e historial
✅ **Nóminas**: Visualización y descarga de nóminas en PDF
✅ **Notificaciones**: Alertas de fichaje, aprobaciones, mensajes
✅ **Directorio**: Listado filtrable de todos los empleados

---

### 🔹 Módulo Jefe Departamento (8 pantallas)
✅ **Dashboard Equipo**: Estado en tiempo real, alertas, métricas generales
✅ **Control de Presencia**: Fichajes del equipo, validación manuales, exportación CSV
✅ **Gestión de Proyectos**: Horas por empleado y proyecto, asignaciones
✅ **Resúmenes Semanales**: Horas, tareas, extras e incidencias, gráficos de barras
✅ **Gestión de Horarios**: Edición de horarios, teletrabajo, excepciones
✅ **Aprobación de Solicitudes**: Horas extras y cambios de modalidad
✅ **Informes**: KPIs, evolución mensual, gráficos estadísticos, exportación PDF
✅ **Comunicación**: Envío de mensajes al equipo, historial de comunicados

---

## 🎨 Diseño Visual Corporativo

**Paleta de colores oficial:**
- 🔵 Azul marino principal: `#1a2744`
- 🟡 Dorado accent: `#c9a84c`
- ⚪ Blanco: `#ffffff`
- 🩶 Gris fondo: `#f5f6fa`
- 🩶 Gris texto: `#6b7280`

**Características diseño:**
- Tipografía: `Segoe UI`, system-ui, sans-serif
- Cards con sombra suave y bordes redondeados
- Sidebar lateral en escritorio, navegación inferior en móvil
- Iconos SVG inline
- Gráficos Canvas nativos con animaciones premium
- Diseño responsive para todos los dispositivos

---

## ⚙️ Características Técnicas Implementadas

✅ Autenticación con sesiones PHP nativas `session_start()`
✅ Contraseñas cifradas con `password_hash()` y `password_verify()`
✅ Todas las consultas MySQL con **PDO Prepared Statements**
✅ Función `getUserIP()` con `$_SERVER['REMOTE_ADDR']` sin filtros
✅ Instalador con verificación de requisitos
✅ Protección de archivos sensibles mediante `.htaccess`
✅ Sin Bootstrap, sin jQuery, sin Chart.js, sin ninguna dependencia externa
✅ Código 100% nativo y sin frameworks de ningún tipo

---

## 📊 Base de Datos

Estructura creada siguiendo estrictamente el orden requerido:
1.  `departamentos`
2.  `empleados`
3.  `horarios`
4.  `proyectos`
5.  `empleados_proyectos`
6.  `fichajes`
7.  `horas_extras`
8.  `solicitudes`
9.  `notificaciones`
10. `nominas`
11. `logs_sistema`

---



## ✅ Aplicación 100% Finalizada

Todas las funcionalidades solicitadas han sido implementadas completamente. La aplicación está lista para ser usada en entorno de producción.

© 2026 NEXORA CONSULTING GROUP. Todos los derechos reservados.