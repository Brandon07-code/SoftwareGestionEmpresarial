# Software de Gestión Empresarial (ERP)

> **Institución:** COTECNOVA — Cartago, Valle del Cauca  
> **Asignatura:** Software de Gestión Empresarial (2026)  
> **Docente:** Jhon James Cano Sánchez  
> **Integrantes:** Brandon Cortés Giraldo & Johan  
> **Caso de Estudio:** Barbería y Perfumería JyM (Cartago, Valle)  
> **Stack Tecnológico:** Laravel 13 · PHP 8.2+ · Eloquent ORM · MySQL / MariaDB · Docker  

---

## 📁 Estructura y Documentación del Proyecto

| Carpeta / Archivo | Descripción |
|-------------------|-------------|
| [📊 `docs/analisis.md`](./docs/analisis.md) | **Análisis de la Empresa** (Formato oficial Guía Evaluativa SGE) |
| [📖 `docs/diccionario.md`](./docs/diccionario.md) | **Diccionario de Datos** exhaustivo (21 tablas del ERP JyM) |
| [🖼️ `docs/diagrama_mer.png`](./docs/diagrama_mer.png) | **Diagrama Entidad-Relación (MER)** |
| [📐 `DIAGRAMA_MER.md`](./DIAGRAMA_MER.md) | Diagramas modulares Mermaid en código |
| [📁 `src/`](./src/) | **Código fuente en Laravel 13** (Modelos, Migraciones, Seeders, Vistas Blade y Rutas Resource) |
| [📁 `Clase2/`](./Clase2/) | Evidencias de instalación del entorno (PHP, Composer, WSL2) |
| [📁 `Clase4/`](./Clase4/) | Evidencias de Migraciones, Modelos, Tinker y BD (Clase 4) |
| [📁 `Clase5/`](./Clase5/) | Evidencias de Controlador Resource, Vistas Blade y Scopes (Clase 5) |
| [📄 `propuestas.md`](./propuestas.md) | Propuestas iniciales de negocio analizadas para la asignatura |

---

## 💈 Módulo Funcional Implementado (Clase 4 - 5)

En la carpeta [`src/`](./src/) se encuentra implementado el núcleo transaccional del ERP:

1. **Migraciones con Relaciones de Clave Foránea:**
   * `clientes` ➔ Datos de contacto y puntos de fidelización.
   * `servicios` ➔ Catálogo por categorías (Barbería, Peluquería, Estética, Spa) con precios en COP.
   * `citas` ➔ Entidad central vinculada a clientes y servicios, con profesional asignado, estados y método de pago.
2. **Modelos Eloquent con Relaciones Activas:**
   * `Cliente` (`hasMany` Cita)
   * `Servicio` (`hasMany` Cita)
   * `Cita` (`belongsTo` Cliente, `belongsTo` Servicio)
3. **Controlador Resource con Optimización Eager Loading:**
   * [`CitaController`](./src/app/Http/Controllers/CitaController.php) implementa `Route::resource('citas')`.
   * El método `index()` hace uso estricto de **`with(['cliente', 'servicio'])`** para eliminar el problema de rendimiento N+1.
4. **Seeders Realistas (+10 registros por tabla):**
   * Población de base de datos con clientes y servicios reales de Cartago mediante `DatabaseSeeder`.
5. **Vistas Blade Responsivas:**
   * Layout maestro con Tailwind CSS en [`resources/views/layouts/app.blade.php`](./src/resources/views/layouts/app.blade.php).
   * CRUD completo: Listado paginado con métricas (`index.blade.php`), formulario de creación con validación (`create.blade.php`), edición (`edit.blade.php`) y comprobante con **QR de Pago** (`show.blade.php`).

---

## 🚀 Instalación y Puesta en Marcha Local

### 1. Clonar el repositorio y entrar a la aplicación
```bash
git clone https://github.com/Brandon07-code/SoftwareGestionEmpresarial.git
cd SoftwareGestionEmpresarial/src
```

### 2. Instalar dependencias y configurar entorno
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Ejecutar migraciones con datos de prueba
```bash
php artisan migrate:fresh --seed
```

### 4. Levantar servidor de desarrollo
```bash
php artisan serve
```
Acceder en el navegador a: **`http://127.0.0.1:8000`**

---

## 🐳 Entorno con Docker (Puertos Protegidos)

El proyecto cuenta con contenedor Docker configurado para evitar colisiones de puertos:
- **Servidor Web PHP / Apache** ➔ `http://localhost:8085`
- **Base de Datos MariaDB** ➔ Puerto local `3307`
- **phpMyAdmin** ➔ `http://localhost:8086`

---

*Cartago, Valle del Cauca — 2026*
