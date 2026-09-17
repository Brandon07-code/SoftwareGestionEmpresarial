# Evidencias de Entrega — Clase 5: Controladores Resource, Vistas Blade y Eloquent Avanzado
## Arquitectura MVC, Eager Loading y Scopes Reutilizables en el ERP

> **Institución:** COTECNOVA — Cartago, Valle del Cauca  
> **Asignatura:** Software de Gestión Empresarial (2026)  
> **Docente:** Jhon James Cano Sánchez  
> **Integrantes:** Brandon Cortés Giraldo & Johan  
> **Caso de Estudio:** Barbería y Perfumería JyM  
> **Repositorio Oficial:** [https://github.com/Brandon07-code/SoftwareGestionEmpresarial](https://github.com/Brandon07-code/SoftwareGestionEmpresarial)  

---

## 1. Temas Desarrollados (Clase 5)

En esta sesión se completó el flujo transaccional de la entidad principal del ERP (**Citas de Barbería y Servicios**), articulando el patrón MVC con buenas prácticas de rendimiento en Laravel 13:

1. **Controlador Resource:**
   * Archivo: [`src/app/Http/Controllers/CitaController.php`](../src/app/Http/Controllers/CitaController.php)
   * Implementa los métodos RESTful estándar: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`.

2. **Rutas Resource:**
   * Archivo: [`src/routes/web.php`](../src/routes/web.php)
   * Definición declarativa mediante `Route::resource('citas', CitaController::class)` generando las 7 rutas RESTful de Laravel.

3. **Optimización con Eager Loading (`with`):**
   * En el método `index()`, se precargan las relaciones con `Cliente` y `Servicio`:
     ```php
     $citas = Cita::with(['cliente', 'servicio'])
         ->orderBy('fecha_hora', 'desc')
         ->paginate(10);
     ```
   * **Beneficio técnico:** Elimina el problema de rendimiento **N+1 queries**, reduciendo de decenas de consultas a solo 3 queries SQL optimizadas.

4. **Scopes Locales Reutilizables:**
   * Archivo: [`src/app/Models/Cita.php`](../src/app/Models/Cita.php)
   * `scopeHoy($query)`: Filtra citas agendadas para la fecha actual (`today()`).
   * `scopePendientes($query)`: Filtra citas con estado `pendiente` o `confirmada`.
   * `scopeCompletadas($query)`: Filtra citas concluidas y facturadas.

5. **Vistas Blade Responsivas (CRUD Completo):**
   * Layout base: [`src/resources/views/layouts/app.blade.php`](../src/resources/views/layouts/app.blade.php) con diseño minimalista sin bordes de contenedor y paleta neutra Slate/Charcoal.
   * `index.blade.php`: Tablero de métricas (total citas, citas de hoy, pendientes, ingresos) y tabla paginada con badges de estado.
   * `create.blade.php`: Formulario con selectores de clientes, catálogo de servicios, profesional asignado y validaciones `@error`.
   * `edit.blade.php`: Edición integral de fechas, estados operativos y método de pago.
   * `show.blade.php`: Comprobante de servicio y módulo de cobro con **QR de Pago**.

---

## 2. Puesta en Marcha y Verificación

Para ejecutar y probar las vistas de la Clase 5 en local:

```bash
cd /home/brandon/SoftwareGestionEmpresarial/src
php artisan serve
```

Navegar a: **`http://127.0.0.1:8000/citas`**

---
*Cartago, Valle del Cauca — Septiembre 2026*
