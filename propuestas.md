# 📋 Propuestas de Proyecto ERP

> **Asignatura:** Software de Gestión Empresarial  
> **Institución:** Cotecnova — Cartago, Valle del Cauca  
> **Stack:** Laravel · PHP · Eloquent ORM · MySQL · Docker  

Todas las propuestas están contextualizadas al tejido empresarial de **Cartago, Valle del Cauca**, enfocadas en pequeñas y medianas empresas de servicio.  
Todas comparten como diferenciador técnico un **QR de confirmación de pago**: el sistema genera un token único por transacción, el cliente escanea el QR con su celular, visualiza el resumen del servicio y al confirmar, el estado se actualiza automáticamente en el sistema — sin integración con pasarelas de pago externas.

---

## 🎉 Propuesta 1 — MontaYa
### Sistema de Gestión para Empresas de Alquiler de Mobiliario y Eventos Sociales

> *"La empresa de decoraciones lleva 15 quinceañeras al mes, presta 400 sillas, 30 mesas y 200 copas — todo anotado en libretas. Siempre falta algo o llega tarde."*

**Problema que resuelve:** Las empresas de alquiler de mobiliario para eventos en Cartago operan sin control de inventario cruzado por fechas. No saben si pueden aceptar un nuevo evento el mismo sábado sin quedar cortos de artículos, y el control de daños o faltantes al devolver el mobiliario se hace de memoria.

### Módulos

| Módulo | Descripción |
|--------|-------------|
| 🪑 **Inventario con disponibilidad cruzada por fecha** | Cada artículo tiene stock total. Al confirmar un evento, el sistema resta los artículos reservados del disponible para esa fecha. Si no hay suficiente, alerta inmediatamente. |
| 📅 **Calendario de eventos con validación de conflictos** | Vista mensual de todos los eventos. Al crear uno nuevo, valida disponibilidad de artículos automáticamente. |
| 👷 **Asignación de personal y logística de montaje** | Cada evento asigna montadores, vehículo y horario. Al finalizar el desmontaje, el personal registra qué artículos regresaron y en qué estado. |
| 🔍 **Control de daños y cobros adicionales** | Artículos rotos o faltantes quedan registrados y generan un cobro adicional vinculado al cliente del evento. |
| 💳 **QR de confirmación de pago** | Al cierre del evento, el cliente escanea el QR, ve el resumen (artículos + cobros adicionales) y confirma. El evento cierra como *Pagado* automáticamente. |

**Diferenciador técnico:** La disponibilidad cruzada de artículos por fecha es un modelo de datos que demuestra relaciones Eloquent avanzadas con atributos pivote y consultas de agregación por rango de fechas.

---

## 💈 Propuesta 2 — Quedó Pinta
### Sistema Integral de Gestión para Barberías, Salones de Belleza y Centros de Estética

> *"El salón agenda por WhatsApp, pierde citas, no sabe cuánto gana cada estilista y no tiene forma de fidelizar a sus clientes."*

**Problema que resuelve:** Los salones de belleza y barberías de Cartago manejan su operación de forma completamente informal. No existe control de citas por estilista, las comisiones se calculan a mano, el inventario de productos se pierde, y no hay ninguna estrategia de fidelización de clientes.

### Módulos

| Módulo | Descripción |
|--------|-------------|
| 📅 **Calendario de citas por estilista** | Vista semanal por empleado con validación de cruce de horarios. El dueño ve toda la operación en un panel central. |
| 💅 **Catálogo de servicios con duración, precio y comisión** | Cada servicio tiene precio, tiempo estimado y porcentaje de comisión. Al cerrar el turno, el sistema calcula automáticamente lo que le corresponde a cada estilista. |
| 📦 **Inventario de productos con consumo por servicio** | Cada servicio descuenta del inventario los productos utilizados. Alertas de stock mínimo y vencimiento. |
| 🌟 **Programa de fidelización: visitas acumuladas** | Al llegar a N visitas, el sistema activa automáticamente un descuento. Panel de *Clientes sin visitar* con más de 30 días de inactividad. |
| 💳 **QR de confirmación de pago** | El estilista genera el QR del turno. El cliente escanea, ve el detalle del servicio y confirma. El turno cierra como *Pagado* y el total del estilista se actualiza en tiempo real. |

**Diferenciador técnico:** Integra RRHH (comisiones automáticas), inventario, agenda y CRM (fidelización) en un solo sistema. Es la definición exacta de un ERP aplicada a un negocio de servicio local, tanto para barberías como para salones mixtos.

---

## 🍲 Propuesta 3 — SirvaPues
### Sistema de Gestión para Restaurantes, Cafeterías y Servicios de Alimentación

> *"La cafetería toma el pedido en papel, lo pierde en cocina, no controla cuántos almuerzos quedan y al cierre no sabe si ganó o perdió."*

**Problema que resuelve:** Las cafeterías y restaurantes de Cartago no tienen sistema. El almuerzo ejecutivo es el negocio más común de la ciudad y ninguno tiene control real de porciones, tiempos de cocina, rendimiento por mesero ni reporte de cierre de caja.

### Módulos

| Módulo | Descripción |
|--------|-------------|
| 📲 **POS móvil + Kitchen Display System (KDS)** | El mesero toma pedidos desde el celular. En cocina aparece una pantalla con semáforo de urgencia por tiempo. El cocinero marca *Listo* y el mesero recibe aviso. |
| 🍽️ **Menú del día con porciones en vivo** | El administrador carga el menú con porciones disponibles. Al agotarse un plato, desaparece automáticamente del menú. |
| 🗂️ **Panel de mesas con cronómetro de ocupación** | Vista visual de mesas: libre, ocupada, esperando cuenta. Reporte de rotaciones del día. |
| 💳 **QR de mesa para ver cuenta y confirmar pago** | El cliente escanea el QR de su mesa, ve el desglose de lo consumido en tiempo real. Al confirmar, la mesa se libera automáticamente. |
| 📊 **Cierre de caja y reportes gerenciales** | Plato más vendido, ingreso por mesero, tiempo promedio de mesa, merma del día. Comparativo vs. semana anterior. |

**Diferenciador técnico:** El Kitchen Display System (KDS) conecta dos actores en tiempo real (sala y cocina), lo que demuestra una arquitectura de múltiples roles y vistas dentro de un mismo sistema Laravel.

---

## 📊 Comparativa General

| Proyecto | Nicho Local — Cartago | Diferenciador Técnico | Complejidad ERP |
|:---------|:----------------------|:----------------------|:----------------|
| 🎉 **MontaYa** | Empresas de decoraciones y eventos | Disponibilidad cruzada de artículos por fecha | ⭐⭐⭐⭐⭐ |
| 💈 **Quedó Pinta** | Salones de belleza y barberías | Comisiones automáticas + Fidelización integrada | ⭐⭐⭐⭐ |
| 🍲 **SirvaPues** | Restaurantes y cafeterías | KDS en cocina + QR de mesa | ⭐⭐⭐⭐⭐ |

---

*Cartago, Valle del Cauca — Agosto 2026*
