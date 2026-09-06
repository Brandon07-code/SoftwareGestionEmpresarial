# 📊 Análisis del Negocio — Barbería y Perfumería JyM ERP

> **Empresa:** Barbería y Perfumería JyM  
> **Ubicación:** Cartago, Valle del Cauca  
> **Asignatura:** Software de Gestión Empresarial — COTECNOVA 2026  
> **Docente:** Jhon James Cano Sánchez  
> **Integrantes:** Brandon Cortés Giraldo & Johan  

---

## 1. Datos Generales de la Empresa

* **Nombre Comercial:** Barbería y Perfumería JyM.
* **Ubicación:** Cartago, Valle del Cauca, Colombia.
* **Actividad Económica:** Prestación de servicios integrales de barbería, estilismo capilar masculino y venta minorista (retail) de perfumería, lociones importadas/réplicas y productos para el cuidado de barba y cabello.
* **Público Objetivo:** Clientes masculinos y familias de Cartago que demandan servicios de motilada rápida, perfilado de barba, tratamientos y productos de cuidado personal.
* **Propuesta de Valor del ERP:**
  * Reemplazar las cuentas en papel y libretas de comisiones de los barberos por un cálculo digital automatizado en tiempo real.
  * Controlar las existencias y mermas de productos de perfumería mediante un kárdex de inventario.
  * Proveer un punto de venta (POS) ágil con arqueo diario de caja y pagos mediante confirmación QR.

---

## 2. Procesos Clave del Negocio

```
1. Solicitud & Agenda ➔ 2. Ejecución del Turno ➔ 3. Facturación en Caja (POS) ➔ 4. Liquidación Comisión ➔ 5. Fidelización
```

1. **Gestión de Turnos y Agenda:**
   * Recepción del cliente por cita previa o por orden de llegada.
   * Asignación del barbero según disponibilidad de horarios (`horarios_empleados`) y habilidades (`empleado_servicio`).
2. **Ciclo de Vida de la Cita:**
   * La cita avanza por los estados: `pendiente` ➔ `confirmada` ➔ `en_atencion` ➔ `completada` (o `cancelada` / `no_asistio`).
3. **Punto de Venta (POS) & Venta Mixta:**
   * Al terminar el turno, se genera el ticket en `ventas`.
   * Permite facturar servicios realizados (`venta_servicio`) más productos de perfumería adquiridos en mostrador (`venta_producto`).
   * Desacople: permite registrar ventas de perfumes a personas que entran sin cita (`cita_id` NULL).
4. **Liquidación Automática de Comisiones:**
   * Por cada servicio completado en la venta, el ERP calcula la comisión del barbero (ej. 50%) y la registra en `comisiones` con estado `pendiente` hasta su pago semanal/diario.
5. **Control de Inventario y Kárdex:**
   * Cada producto vendido descuenta stock automáticamente y genera un registro inmutable en `movimientos_inventario`.
6. **Arqueo y Cierre de Caja:**
   * Control de saldo inicial y saldo final diario por usuario de apertura en `cajas` y `movimientos_caja`.

---

## 3. Entidades y Tablas del ERP

El modelo de datos completo cuenta con **21 entidades** organizadas en 5 módulos (ver detalle técnico y relaciones en [`DIAGRAMA_MER.md`](./DIAGRAMA_MER.md)):

1. **Usuarios & Personal:** `roles`, `usuarios`, `empleados`, `horarios_empleados`, `empleado_servicio`.
2. **Clientes & Fidelización:** `clientes`, `movimientos_puntos`.
3. **Servicios & Agenda:** `categorias_servicios`, `servicios`, `citas`, `cita_servicio`.
4. **Inventario & Perfumería:** `categorias_productos`, `productos`, `movimientos_inventario`.
5. **Finanzas & POS:** `ventas`, `venta_servicio`, `venta_producto`, `pagos`, `cajas`, `movimientos_caja`, `comisiones`.

---

## 4. Preguntas Guía de la Asignatura

### ¿Qué información se guarda de un cliente?
Datos de contacto (nombre, teléfono/WhatsApp, dirección), saldo de puntos acumulados por fidelidad, notas de preferencias personales (tipo de corte habitual, alergias a lociones/tintes) y su historial de visitas.

### ¿Qué información se guarda de un producto?
Nombre del perfume o insumo, categoría, costo de adquisición, precio de venta al público, stock actual disponible y stock mínimo para emitir alertas de reabastecimiento antes de agotarse.

### ¿Cómo se relaciona una venta con el inventario y las comisiones?
La tabla central `ventas` vincula:
* Los productos a través de `venta_producto`, que mediante transacciones (`DB::transaction`) descuenta las unidades de `productos` y crea un registro en `movimientos_inventario`.
* Los servicios a través de `venta_servicio`, que registra al empleado responsable, congela el `precio_historico` y genera automáticamente el registro en `comisiones` para liquidar los honorarios del barbero.
