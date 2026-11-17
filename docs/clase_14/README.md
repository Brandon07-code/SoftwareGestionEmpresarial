# Entregable Clase 14: Automatización — Eventos, Notificaciones, Colas y Tareas Programadas

**Institución:** Corporación de Estudios Tecnológicos del Norte del Valle — **COTECNOVA**  
**Asignatura:** Software de Gestión Empresarial (SGE - 2026)  
**Docente Titular:** Ing. Jhon James Cano Sánchez  
**Estudiantes:** Brandon Cortés Giraldo & Johan Sttive Linares Barragán  
**Plataforma ERP:** Turnix ERP (Barbería & Perfumería JyM S.A.S. / Multi-empresa)  

---

## 1. Objetivos Cumplidos

Se implementó el ciclo integral de automatización para que el ERP reaccione automáticamente a los hechos del negocio sin intervención manual:

1. **Desacoplamiento con Eventos y Listeners:**
   - `SaleRegistered`: Disparado tras confirmar una venta transaccional (`ShouldDispatchAfterCommit`). El listener `CheckLowStock` verifica si el producto cruzó el umbral configurado (`low_stock_threshold = 5`) y notifica a los compradores.
   - `PurchaseReceived`: Disparado al recibir mercancía (parcial o total). El listener `NotifyPurchaseReceived` notifica al creador de la orden y le adjunta el PDF oficial generado en memoria.

2. **Notificaciones Multicanal (`mail` + `database`) y Colas (`ShouldQueue`):**
   - `LowStockNotification`: Alerta de stock crítico enviada por correo y a la tabla de base de datos para la campana de menú.
   - `PurchaseReceivedNotification`: Alerta de mercancía recibida con archivo PDF adjunto (`{$purchase_number}.pdf`).
   - `DailySummaryNotification`: Resumen nocturno con ventas, compras del día y productos en stock bajo.

3. **Tareas Programadas (Scheduler de Laravel):**
   - Comando Artisan `erp:resumen-diario` programado a las 19:00 hora Colombia (`America/Bogota`).
   - Tarea de mantenimiento `activitylog:clean` mensual para depuración de bitácoras antiguas.

4. **Centro de Notificaciones y Campana UI:**
   - Contador dinámico de notificaciones no leídas en el menú superior y menú móvil.
   - Vista web dedicada `/notificaciones` (`notifications.index`) con opciones de abrir, marcar como leída y marcar todas como leídas.

---

## 2. Verificación de Eventos (`event:list`)

```text
App\Events\SaleRegistered
 ⇂ App\Listeners\CheckLowStock@handle

App\Events\PurchaseReceived
 ⇂ App\Listeners\NotifyPurchaseReceived@handle
```

---

## 3. Verificación del Programador (`schedule:list`)

```text
0 19 * * *  php artisan erp:resumen-diario ............. Next Due: 19:00 (America/Bogota)
0 8  * * *  php artisan turnix:check-low-stock ....... Next Due: 08:00
0 0  1 * *  php artisan activitylog:clean ............ Next Due: 1st of month
```

---

## 4. Pruebas Automatizadas (`Clase14Test`)

Se construyó la suite de pruebas unitarias y de integración `tests/Feature/Clase14Test.php` ejecutando con éxito 100% de aserciones:

```text
PASS  Tests\Feature\Clase14Test
✓ sale registered event dispatched and triggers low stock notification
✓ selling already low stock product does not repeat alert
✓ purchase received dispatches event and notifies user with pdf
✓ daily summary command sends notification
✓ notifications web screens and actions
✓ scheduler has registered daily summary and activitylog clean

Tests: 6 passed (21 assertions)
```
