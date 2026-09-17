# 📖 Diccionario de Datos del Sistema ERP
## Barbería y Perfumería JyM

> **Asignatura:** Software de Gestión Empresarial (2026)  
> **Institución:** COTECNOVA — Cartago, Valle del Cauca  
> **Docente:** Jhon James Cano Sánchez  
> **Estudiantes:** Brandon Cortés Giraldo & Johan  

---

## MÓDULO 1: Seguridad, Roles y Usuarios

### 1. `roles`
Catálogo de perfiles y niveles de autorización en el sistema.
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador único del rol |
| `nombre` | VARCHAR(50) | NOT NULL, UNIQUE | Nombre del rol (Administrador, Recepcionista, Barbero) |
| `estado` | BOOLEAN | NOT NULL, DEFAULT TRUE | Estado operativo del rol |

### 2. `usuarios`
Cuentas de acceso y credenciales del personal.
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador único del usuario |
| `role_id` | BIGINT UNSIGNED | FK ➔ roles(id) | Rol asignado |
| `primer_nombre` | VARCHAR(50) | NOT NULL | Primer nombre |
| `segundo_nombre` | VARCHAR(50) | NULLABLE | Segundo nombre |
| `primer_apellido` | VARCHAR(50) | NOT NULL | Primer apellido |
| `segundo_apellido` | VARCHAR(50) | NULLABLE | Segundo apellido |
| `email` | VARCHAR(150) | NOT NULL, UNIQUE | Correo electrónico corporativo o personal |
| `password` | VARCHAR(255) | NOT NULL | Contraseña con hash bcrypt |
| `telefono` | VARCHAR(20) | NULLABLE | Número telefónico |
| `direccion` | VARCHAR(200) | NULLABLE | Dirección de residencia |
| `estado` | BOOLEAN | DEFAULT TRUE | Estado del usuario |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Marcas temporales |

---

## MÓDULO 2: Personal y Gestión Laboral

### 3. `empleados`
Ficha profesional y esquema de comisiones para barberos y estilistas.
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador del empleado |
| `user_id` | BIGINT UNSIGNED | FK ➔ usuarios(id), UNIQUE | Usuario del sistema vinculado |
| `especialidad` | VARCHAR(100) | NULLABLE | Especialidad (Barbería clásica, Colorimetría, etc.) |
| `tipo_comision` | ENUM('porcentaje', 'valor_fijo') | NOT NULL | Esquema de liquidación |
| `valor_comision` | DECIMAL(10,2) | NOT NULL | Valor (ej. 50.00 para 50%) |
| `estado` | BOOLEAN | DEFAULT TRUE | Estado activo/inactivo |

### 4. `horarios_empleados`
Disponibilidad laboral por día y franja horaria.
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador del horario |
| `empleado_id` | BIGINT UNSIGNED | FK ➔ empleados(id) | Empleado asignado |
| `dia_semana` | TINYINT UNSIGNED | NOT NULL | Día (1=Lunes, 7=Domingo) |
| `hora_inicio` | TIME | NOT NULL | Hora de entrada |
| `hora_fin` | TIME | NOT NULL | Hora de salida |
| `disponible` | BOOLEAN | DEFAULT TRUE | Disponibilidad para turnos |

### 5. `empleado_servicio`
Matriz de competencias (servicios que cada barbero está capacitado para realizar).
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `empleado_id` | BIGINT UNSIGNED | PK, FK ➔ empleados(id) | Barbero |
| `servicio_id` | BIGINT UNSIGNED | PK, FK ➔ servicios(id) | Servicio habilitado |

---

## MÓDULO 3: Clientes y Fidelización (CRM)

### 6. `clientes`
Directorio de clientes atendidos en Cartago.
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador del cliente |
| `user_id` | BIGINT UNSIGNED | FK ➔ usuarios(id), NULLABLE | Cuenta opcional |
| `primer_nombre` | VARCHAR(50) | NOT NULL | Nombre |
| `primer_apellido` | VARCHAR(50) | NOT NULL | Apellido |
| `telefono` | VARCHAR(20) | NOT NULL | WhatsApp / teléfono |
| `direccion` | VARCHAR(200) | NULLABLE | Barrio o dirección |
| `puntos` | INT UNSIGNED | DEFAULT 0 | Saldo de puntos de fidelización |
| `preferencias` | TEXT | NULLABLE | Notas de corte, alergias o visagismo |
| `estado` | BOOLEAN | DEFAULT TRUE | Estado del cliente |

### 7. `movimientos_puntos`
Kárdex de lealtad y puntos.
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador del movimiento |
| `cliente_id` | BIGINT UNSIGNED | FK ➔ clientes(id) | Cliente beneficiario |
| `venta_id` | BIGINT UNSIGNED | FK ➔ ventas(id), NULLABLE | Venta originadora |
| `tipo` | ENUM('ganancia', 'redencion', 'ajuste') | NOT NULL | Motivo |
| `puntos` | INT | NOT NULL | Puntos sumados o restados |
| `saldo_anterior` | INT UNSIGNED | NOT NULL | Saldo previo |
| `saldo_nuevo` | INT UNSIGNED | NOT NULL | Saldo resultante |

---

## MÓDULO 4: Catálogo y Citas (Core Transaccional)

### 8. `categorias_servicios`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `nombre` | VARCHAR(100) | UNIQUE, NOT NULL | Barbería, Peluquería, Spa, Estética |
| `estado` | BOOLEAN | DEFAULT TRUE | Estado |

### 9. `servicios`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `categoria_servicio_id`| BIGINT UNSIGNED | FK ➔ categorias_servicios(id) | Categoría |
| `nombre` | VARCHAR(100) | NOT NULL | Nombre del servicio |
| `precio` | DECIMAL(10,2) | NOT NULL | Precio de venta actual |
| `duracion_minutos` | SMALLINT UNSIGNED | NOT NULL | Minutos en sillón |
| `estado` | BOOLEAN | DEFAULT TRUE | Estado |

### 10. `citas`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `cliente_id` | BIGINT UNSIGNED | FK ➔ clientes(id) | Cliente |
| `empleado_id` | BIGINT UNSIGNED | FK ➔ empleados(id) | Barbero que atiende |
| `fecha` | DATE | NOT NULL | Fecha programada |
| `hora_inicio` | TIME | NOT NULL | Hora de inicio |
| `hora_fin` | TIME | NOT NULL | Hora de fin |
| `estado` | ENUM | NOT NULL | pendiente, confirmada, en_atencion, completada, cancelada, no_asistio |
| `total` | DECIMAL(10,2) | DEFAULT 0 | Total estimado |

### 11. `cita_servicio`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `cita_id` | BIGINT UNSIGNED | PK, FK ➔ citas(id) | Cita |
| `servicio_id` | BIGINT UNSIGNED | PK, FK ➔ servicios(id) | Servicio |
| `precio_historico` | DECIMAL(10,2) | NOT NULL | Precio congelado al agendar |
| `duracion_historica` | SMALLINT UNSIGNED | NOT NULL | Duración congelada |

---

## MÓDULO 5: Retail, Perfumería e Inventario

### 12. `categorias_productos`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `nombre` | VARCHAR(100) | UNIQUE, NOT NULL | Perfumería, Ceras, Cuidado de barba |

### 13. `productos`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `categoria_producto_id`| BIGINT UNSIGNED | FK ➔ categorias_productos(id) | Categoría |
| `nombre` | VARCHAR(150) | NOT NULL | Nombre del perfume/producto |
| `costo` | DECIMAL(10,2) | NOT NULL | Costo de compra |
| `precio_venta` | DECIMAL(10,2) | NOT NULL | Precio al público |
| `stock_actual` | INT UNSIGNED | DEFAULT 0 | Unidades en bodega/vitrina |
| `stock_minimo` | INT UNSIGNED | DEFAULT 0 | Alerta de reposición |

### 14. `movimientos_inventario` (Kárdex)
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `producto_id` | BIGINT UNSIGNED | FK ➔ productos(id) | Producto |
| `tipo` | ENUM('entrada','salida','ajuste') | NOT NULL | Tipo de flujo |
| `cantidad` | INT UNSIGNED | NOT NULL | Cantidad |
| `stock_anterior` | INT UNSIGNED | NOT NULL | Stock previo |
| `stock_nuevo` | INT UNSIGNED | NOT NULL | Stock final |
| `motivo` | VARCHAR(255) | NULLABLE | Venta, compra, merma |

---

## MÓDULO 6: Ventas, POS, Pagos, Caja y Comisiones

### 15. `ventas`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Número de factura / ticket |
| `cliente_id` | BIGINT UNSIGNED | FK ➔ clientes(id), NULLABLE | Cliente comprador |
| `cita_id` | BIGINT UNSIGNED | FK ➔ citas(id), NULLABLE | Turno asociado (opcional) |
| `usuario_id` | BIGINT UNSIGNED | FK ➔ usuarios(id) | Cajero que factura |
| `subtotal_servicios` | DECIMAL(10,2) | DEFAULT 0 | Monto por servicios |
| `subtotal_productos` | DECIMAL(10,2) | DEFAULT 0 | Monto por perfumería |
| `total` | DECIMAL(10,2) | NOT NULL | Total a pagar |
| `estado` | ENUM('pendiente','pagada','anulada') | NOT NULL | Estado |

### 16. `venta_servicio`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Detalle de servicio |
| `venta_id` | BIGINT UNSIGNED | FK ➔ ventas(id) | Venta |
| `servicio_id` | BIGINT UNSIGNED | FK ➔ servicios(id) | Servicio |
| `empleado_id` | BIGINT UNSIGNED | FK ➔ empleados(id) | Barbero que cobró |
| `precio_historico` | DECIMAL(10,2) | NOT NULL | Tarifa congelada |
| `porcentaje_comision` | DECIMAL(5,2) | NOT NULL | % congelado |
| `valor_comision` | DECIMAL(10,2) | NOT NULL | Comisión liquidada en $ |

### 17. `venta_producto`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Detalle de producto |
| `venta_id` | BIGINT UNSIGNED | FK ➔ ventas(id) | Venta |
| `producto_id` | BIGINT UNSIGNED | FK ➔ productos(id) | Producto |
| `cantidad` | INT UNSIGNED | NOT NULL | Cantidad vendida |
| `precio_historico` | DECIMAL(10,2) | NOT NULL | Precio congelado |
| `subtotal` | DECIMAL(10,2) | NOT NULL | Cantidad * Precio |

### 18. `pagos`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `venta_id` | BIGINT UNSIGNED | FK ➔ ventas(id) | Factura |
| `metodo` | ENUM('efectivo','transferencia','qr') | NOT NULL | Canal de pago |
| `monto` | DECIMAL(10,2) | NOT NULL | Monto pagado |
| `referencia` | VARCHAR(150) | NULLABLE | Código de transacción |
| `estado` | ENUM('pendiente','confirmado','rechazado') | NOT NULL | Estado |

### 19. `cajas`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Turno de caja |
| `usuario_apertura_id` | BIGINT UNSIGNED | FK ➔ usuarios(id) | Responsable |
| `fecha` | DATE | NOT NULL | Fecha operativa |
| `saldo_inicial` | DECIMAL(10,2) | NOT NULL | Base de caja |
| `saldo_final` | DECIMAL(10,2) | NULLABLE | Cierre de caja |
| `estado` | ENUM('abierta','cerrada') | NOT NULL | Estado |

### 20. `movimientos_caja`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Movimiento |
| `caja_id` | BIGINT UNSIGNED | FK ➔ cajas(id) | Caja |
| `usuario_id` | BIGINT UNSIGNED | FK ➔ usuarios(id) | Usuario |
| `tipo` | ENUM('ingreso','egreso','ajuste') | NOT NULL | Flujo |
| `concepto` | VARCHAR(255) | NOT NULL | Motivo |
| `monto` | DECIMAL(10,2) | NOT NULL | Valor del flujo |

### 21. `comisiones`
| Campo | Tipo SQL | Restricción | Descripción |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AI | Identificador |
| `empleado_id` | BIGINT UNSIGNED | FK ➔ empleados(id) | Barbero |
| `venta_id` | BIGINT UNSIGNED | FK ➔ ventas(id) | Venta origen |
| `venta_servicio_id` | BIGINT UNSIGNED | FK ➔ venta_servicio(id) | Detalle de servicio |
| `porcentaje` | DECIMAL(5,2) | NOT NULL | % aplicado |
| `base_calculo` | DECIMAL(10,2) | NOT NULL | Monto base |
| `valor` | DECIMAL(10,2) | NOT NULL | Comisión en $ |
| `estado` | ENUM('pendiente','liquidada','pagada') | NOT NULL | Estado de pago al barbero |
