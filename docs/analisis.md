# Análisis de la Empresa

> **Institución:** COTECNOVA — Cartago, Valle del Cauca  
> **Asignatura:** Software de Gestión Empresarial (2026)  
> **Docente:** Jhon James Cano Sánchez  
> **Integrantes:** Brandon Cortés Giraldo & Johan  
> **Empresa Seleccionada:** Barbería y Perfumería JyM  

---

## 1. Datos Generales

- **Nombre:** Barbería y Perfumería JyM
- **Giro del negocio:** Prestación de servicios de cuidado personal masculino (cortes de cabello, degradados, ritual de barba, cejas, mascarillas capilares) y comercialización minorista de perfumería (lociones originales y réplicas), ceras, pomadas y tónicos capilares.
- **Tamaño:** Pequeña empresa (Microempresa / PyME local de servicios comerciales).

---

## 2. Procesos Clave

- **Ventas:** 
  El cliente puede llegar por cita previa agendada en el sistema o por orden de llegada al establecimiento. El servicio es prestado por el barbero asignado. Al finalizar, se genera un ticket unificado en el Punto de Venta (POS) que puede incluir tanto el servicio de barbería como productos de perfumería que el cliente decida comprar. El cobro se realiza en efectivo, transferencia bancaria o mediante escaneo de **QR de Pago**. Cada servicio facturado liquida automáticamente el porcentaje de comisión acordado con el barbero (ej. 50%).

- **Compras:**
  La barbería realiza compras periódicas de reabastecimiento a distribuidores de perfumería e insumos capilares (tintes, cuchillas desechables, alcohol, papel cuello, ceras). Cada orden de compra recibida se ingresa al sistema para actualizar el costo y reponer el inventario disponible.

- **Inventario:**
  Se gestiona mediante un kárdex de movimientos (entradas por compra, salidas por venta directa y bajas por merma o consumo interno de insumos). Cada producto cuenta con un umbral de `stock_minimo`; cuando las unidades bajan de dicho límite, el sistema emite una alerta preventiva para evitar desabastecimiento.

- **Otros (Clientes y Fidelización):**
  Registro de clientes con nombre, WhatsApp y preferencias de corte. El sistema incorpora un programa de fidelización donde cada visita o monto facturado otorga puntos acumulables que pueden canjearse por descuentos en próximos turnos.

---

## 3. Entidades Identificadas (Tablas del Sistema ERP)

El sistema contempla un ecosistema de 21 tablas organizadas modularmente:
1. **Usuarios y Accesos:** `roles`, `usuarios`.
2. **Personal y Horarios:** `empleados`, `horarios_empleados`, `empleado_servicio`.
3. **Clientes y Fidelización:** `clientes`, `movimientos_puntos`.
4. **Catálogo y Citas:** `categorias_servicios`, `servicios`, `citas`, `cita_servicio`.
5. **Inventario y Retail:** `categorias_productos`, `productos`, `movimientos_inventario`.
6. **Finanzas, POS y Caja:** `ventas`, `venta_servicio`, `venta_producto`, `pagos`, `cajas`, `movimientos_caja`, `comisiones`.

---

## 4. Diccionario de Datos (Entidades Centrales del Avance)

### Tabla: `clientes`
| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto-increment | Identificador único del cliente |
| `user_id` | BIGINT UNSIGNED | FK, Nullable | Enlace opcional a cuenta de usuario web |
| `nombre` | VARCHAR(255) | NOT NULL | Nombre completo del cliente |
| `telefono` | VARCHAR(20) | NOT NULL | Número de contacto o WhatsApp |
| `email` | VARCHAR(255) | NULLABLE | Correo electrónico |
| `direccion` | VARCHAR(255) | NULLABLE | Dirección en Cartago |
| `puntos_fidelizacion` | INT | DEFAULT 0 | Saldo de puntos acumulados |
| `notas` | TEXT | NULLABLE | Preferencias de corte o alergias |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Trazabilidad de auditoría |

### Tabla: `servicios`
| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto-increment | Identificador único del servicio |
| `categoria` | VARCHAR(100) | NOT NULL | Categoría (Barbería, Peluquería, Spa, Estética) |
| `nombre` | VARCHAR(255) | NOT NULL | Nombre comercial del servicio |
| `precio` | DECIMAL(10,2) | NOT NULL | Precio de venta al público en COP |
| `duracion_minutos` | INT | DEFAULT 30 | Tiempo estimado de duración en sillón |
| `descripcion` | TEXT | NULLABLE | Detalles del procedimiento |
| `activo` | BOOLEAN | DEFAULT TRUE | Disponibilidad en agenda |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Trazabilidad de auditoría |

### Tabla: `citas` (Entidad Transaccional Principal)
| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto-increment | Número de turno / cita |
| `cliente_id` | BIGINT UNSIGNED | FK ➔ clientes(id) | Cliente asociado a la cita |
| `servicio_id` | BIGINT UNSIGNED | FK ➔ servicios(id) | Servicio contratado |
| `estilista` | VARCHAR(100) | NOT NULL | Profesional o barbero que atiende |
| `fecha_hora` | DATETIME | NOT NULL | Fecha y hora programada |
| `estado` | ENUM | NOT NULL, DEFAULT 'pendiente' | pendiente, confirmada, en_atencion, completada, cancelada |
| `total` | DECIMAL(10,2) | NOT NULL | Valor total del servicio |
| `metodo_pago` | ENUM | NOT NULL, DEFAULT 'pendiente' | efectivo, transferencia, qr_fachada, pendiente |
| `notas` | TEXT | NULLABLE | Observaciones especiales del turno |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Trazabilidad de auditoría |

---

## 5. Diagrama Entidad-Relación (MER)

![Diagrama MER](./diagrama_mer.png)
