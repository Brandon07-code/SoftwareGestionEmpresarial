# 📐 Modelo Entidad - Relación (MER)
## Sistema de Gestión Empresarial (ERP) — Barbería y Perfumería JyM

> **Institución:** COTECNOVA — Cartago, Valle del Cauca  
> **Asignatura:** Software de Gestión Empresarial (2026)  
> **Docente:** Jhon James Cano Sánchez  
> **Estudiantes:** Brandon Cortés Giraldo & Johan  
> **Caso de Estudio:** Barbería y Perfumería JyM (Cartago, Valle)  

---

## 1. Diagramas MER Modulares (Renderizado Vectorial SVG Nítido en GitHub)

Para garantizar legibilidad total sin pixelado ni texto borroso, el modelo relacional (21 entidades) se desglosa a continuación en **diagramas vectoriales nativos de Mermaid**, organizados por submódulos con todos sus campos, tipos de datos, llaves primarias (PK) y foráneas (FK):

---

### 💈 Submódulo A: Usuarios, Personal, Clientes y Agenda (Citas & Servicios)

```mermaid
erDiagram
    ROLES {
        bigint_unsigned id PK
        varchar nombre
        boolean estado
    }

    USUARIOS {
        bigint_unsigned id PK
        bigint_unsigned role_id FK
        varchar primer_nombre
        varchar segundo_nombre
        varchar primer_apellido
        varchar segundo_apellido
        varchar email
        varchar password
        varchar telefono
        varchar direccion
        boolean estado
    }

    EMPLEADOS {
        bigint_unsigned id PK
        bigint_unsigned user_id FK
        varchar especialidad
        enum tipo_comision
        decimal valor_comision
        boolean estado
    }

    HORARIOS_EMPLEADOS {
        bigint_unsigned id PK
        bigint_unsigned empleado_id FK
        tinyint_unsigned dia_semana
        time hora_inicio
        time hora_fin
        boolean disponible
    }

    CLIENTES {
        bigint_unsigned id PK
        bigint_unsigned user_id FK
        varchar primer_nombre
        varchar segundo_nombre
        varchar primer_apellido
        varchar segundo_apellido
        varchar telefono
        varchar direccion
        int_unsigned puntos
        text preferencias
        text observaciones
        boolean estado
    }

    CATEGORIAS_SERVICIOS {
        bigint_unsigned id PK
        varchar nombre
        boolean estado
    }

    SERVICIOS {
        bigint_unsigned id PK
        bigint_unsigned categoria_servicio_id FK
        varchar nombre
        text descripcion
        decimal precio
        smallint_unsigned duracion_minutos
        boolean estado
    }

    EMPLEADO_SERVICIO {
        bigint_unsigned empleado_id PK,FK
        bigint_unsigned servicio_id PK,FK
    }

    CITAS {
        bigint_unsigned id PK
        bigint_unsigned cliente_id FK
        bigint_unsigned empleado_id FK
        date fecha
        time hora_inicio
        time hora_fin
        enum estado
        decimal total
        text observaciones
    }

    CITA_SERVICIO {
        bigint_unsigned cita_id PK,FK
        bigint_unsigned servicio_id PK,FK
        decimal precio_historico
        smallint_unsigned duracion_historica
    }

    ROLES ||--o{ USUARIOS : "define permisos"
    USUARIOS ||--o| EMPLEADOS : "perfil barbero"
    USUARIOS ||--o| CLIENTES : "perfil opcional"
    EMPLEADOS ||--o{ HORARIOS_EMPLEADOS : "disponibilidad"
    EMPLEADOS ||--o{ EMPLEADO_SERVICIO : "habilidades"
    SERVICIOS ||--o{ EMPLEADO_SERVICIO : "asignado a"
    CATEGORIAS_SERVICIOS ||--o{ SERVICIOS : "clasifica"
    CLIENTES ||--o{ CITAS : "solicita"
    EMPLEADOS ||--o{ CITAS : "atiende"
    CITAS ||--o{ CITA_SERVICIO : "contiene"
    SERVICIOS ||--o{ CITA_SERVICIO : "incluido en"
```

---

### 🧴 Submódulo B: Retail, Perfumería y Kárdex de Inventario

```mermaid
erDiagram
    CATEGORIAS_PRODUCTOS {
        bigint_unsigned id PK
        varchar nombre
        boolean estado
    }

    PRODUCTOS {
        bigint_unsigned id PK
        bigint_unsigned categoria_producto_id FK
        varchar nombre
        text descripcion
        decimal costo
        decimal precio_venta
        int_unsigned stock_actual
        int_unsigned stock_minimo
        varchar imagen_url
        boolean estado
    }

    MOVIMIENTOS_INVENTARIO {
        bigint_unsigned id PK
        bigint_unsigned producto_id FK
        enum tipo
        int_unsigned cantidad
        int_unsigned stock_anterior
        int_unsigned stock_nuevo
        varchar motivo
        varchar referencia_tipo
        bigint_unsigned referencia_id
        timestamp created_at
    }

    CATEGORIAS_PRODUCTOS ||--o{ PRODUCTOS : "clasifica"
    PRODUCTOS ||--o{ MOVIMIENTOS_INVENTARIO : "registra kardex"
```

---

### 💵 Submódulo C: Facturación (POS), Pagos, Caja, Comisiones y Fidelización

```mermaid
erDiagram
    VENTAS {
        bigint_unsigned id PK
        bigint_unsigned cliente_id FK
        bigint_unsigned cita_id FK
        bigint_unsigned usuario_id FK
        decimal subtotal_servicios
        decimal subtotal_productos
        decimal total
        enum estado
        timestamp created_at
    }

    VENTA_SERVICIO {
        bigint_unsigned id PK
        bigint_unsigned venta_id FK
        bigint_unsigned servicio_id FK
        bigint_unsigned empleado_id FK
        int_unsigned cantidad
        decimal precio_historico
        decimal porcentaje_comision
        decimal valor_comision
    }

    VENTA_PRODUCTO {
        bigint_unsigned id PK
        bigint_unsigned venta_id FK
        bigint_unsigned producto_id FK
        int_unsigned cantidad
        decimal precio_historico
        decimal subtotal
    }

    PAGOS {
        bigint_unsigned id PK
        bigint_unsigned venta_id FK
        enum metodo
        decimal monto
        varchar referencia
        enum estado
        datetime fecha_pago
    }

    CAJAS {
        bigint_unsigned id PK
        bigint_unsigned usuario_apertura_id FK
        date fecha
        decimal saldo_inicial
        decimal saldo_final
        enum estado
        datetime fecha_apertura
        datetime fecha_cierre
    }

    MOVIMIENTOS_CAJA {
        bigint_unsigned id PK
        bigint_unsigned caja_id FK
        bigint_unsigned usuario_id FK
        bigint_unsigned venta_id FK
        enum tipo
        varchar concepto
        decimal monto
        timestamp created_at
    }

    COMISIONES {
        bigint_unsigned id PK
        bigint_unsigned empleado_id FK
        bigint_unsigned venta_id FK
        bigint_unsigned venta_servicio_id FK
        decimal porcentaje
        decimal base_calculo
        decimal valor
        enum estado
    }

    MOVIMIENTOS_PUNTOS {
        bigint_unsigned id PK
        bigint_unsigned cliente_id FK
        bigint_unsigned venta_id FK
        enum tipo
        int puntos
        int_unsigned saldo_anterior
        int_unsigned saldo_nuevo
        varchar descripcion
        timestamp created_at
    }

    VENTAS ||--o{ VENTA_SERVICIO : "servicios facturados"
    VENTAS ||--o{ VENTA_PRODUCTO : "productos facturados"
    VENTAS ||--o{ PAGOS : "transacciones de pago"
    VENTAS ||--o{ COMISIONES : "genera comision barbero"
    VENTAS ||--o{ MOVIMIENTOS_CAJA : "ingreso caja"
    VENTAS ||--o{ MOVIMIENTOS_PUNTOS : "suma puntos"
    CAJAS ||--o{ MOVIMIENTOS_CAJA : "arqueo flujo de caja"
```

---

## 2. Diagrama Global Simplificado (Mapa de Navegación de Entidades)

```mermaid
erDiagram
    ROLES ||--o{ USUARIOS : ""
    USUARIOS ||--o| EMPLEADOS : ""
    USUARIOS ||--o| CLIENTES : ""
    USUARIOS ||--o{ CAJAS : ""
    USUARIOS ||--o{ VENTAS : ""

    EMPLEADOS ||--o{ HORARIOS_EMPLEADOS : ""
    EMPLEADOS ||--o{ EMPLEADO_SERVICIO : ""
    SERVICIOS ||--o{ EMPLEADO_SERVICIO : ""
    EMPLEADOS ||--o{ CITAS : ""
    EMPLEADOS ||--o{ COMISIONES : ""

    CLIENTES ||--o{ CITAS : ""
    CLIENTES ||--o{ VENTAS : ""
    CLIENTES ||--o{ MOVIMIENTOS_PUNTOS : ""

    CATEGORIAS_SERVICIOS ||--o{ SERVICIOS : ""
    CITAS ||--o{ CITA_SERVICIO : ""
    SERVICIOS ||--o{ CITA_SERVICIO : ""
    CITAS ||--o| VENTAS : "factura"

    CATEGORIAS_PRODUCTOS ||--o{ PRODUCTOS : ""
    PRODUCTOS ||--o{ MOVIMIENTOS_INVENTARIO : ""
    PRODUCTOS ||--o{ VENTA_PRODUCTO : ""

    VENTAS ||--o{ VENTA_SERVICIO : ""
    VENTAS ||--o{ VENTA_PRODUCTO : ""
    VENTAS ||--o{ PAGOS : ""
    VENTAS ||--o{ COMISIONES : ""
    CAJAS ||--o{ MOVIMIENTOS_CAJA : ""
    VENTAS ||--o{ MOVIMIENTOS_CAJA : ""
```

---

## 3. Diccionario de Datos Exhaustivo (21 Tablas)

### MÓDULO 1: Identidad, Accesos y Personal
1. **`roles`:** Catálogo de perfiles (`Administrador`, `Recepcionista/Cajero`, `Barbero/Estilista`).
2. **`usuarios`:** Credenciales maestras, nombres, apellidos, correo único, contraseña cifrada, teléfono y estado.
3. **`empleados`:** Perfil profesional del barbero vinculado a su usuario (`user_id` único), especialidad, tipo de liquidación (`porcentaje` o `valor_fijo`) y valor pactado.
4. **`horarios_empleados`:** Matriz de disponibilidad semanal por barbero (`dia_semana`, `hora_inicio`, `hora_fin`, `disponible`).
5. **`empleado_servicio`:** Habilidades profesionales (asocia qué servicios sabe realizar cada barbero).

### MÓDULO 2: Clientes y Fidelización (CRM)
6. **`clientes`:** Ficha de contacto y preferencias. Campo `user_id` **NULLABLE** para permitir registrar clientes de mostrador en Cartago sin correo ni cuenta web.
7. **`movimientos_puntos`:** Kárdex de fidelidad (`ganancia`, `redencion`, `ajuste`) con saldo anterior y nuevo.

### MÓDULO 3: Catálogo y Operación de Citas
8. **`categorias_servicios`:** Familias de atención (`Barbería`, `Peluquería`, `Spa`, `Estética`).
9. **`servicios`:** Tarifario base con precio actual y duración en minutos.
10. **`citas`:** Turnos de atención con ciclo de vida (`pendiente`, `confirmada`, `en_atencion`, `completada`, `cancelada`, `no_asistio`).
11. **`cita_servicio`:** Detalle multiproducto por cita con respaldo inmutable de `precio_historico` y `duracion_historica`.

### MÓDULO 4: Retail, Perfumería y Control de Stock
12. **`categorias_productos`:** Familias de retail (`Lociones Originales`, `Perfumes Réplica`, `Ceras y Pomadas`, `Cuidado de Barba`).
13. **`productos`:** Ficha de inventario con costo, precio de venta, stock actual y umbral de alerta de stock mínimo.
14. **`movimientos_inventario`:** Kárdex de almacén (`entrada`, `salida`, `ajuste`) con trazabilidad de motivo y documento de referencia.

### MÓDULO 5: Ventas, Facturación (POS), Caja y Comisiones
15. **`ventas`:** Ticket unificado de cobro. `cita_id` es **NULLABLE** para permitir compras de perfumería sin turno de motilada.
16. **`venta_servicio`:** Servicios liquidados en el ticket con congelamiento de `precio_historico`, `porcentaje_comision` y `valor_comision`.
17. **`venta_producto`:** Productos físicos facturados con `precio_historico` y subtotal.
18. **`pagos`:** Transacciones financieras (`efectivo`, `transferencia`, `qr`) con monto y código de referencia.
19. **`cajas`:** Turnos diarios de caja con saldo inicial, saldo final y marcas de tiempo de apertura y cierre.
20. **`movimientos_caja`:** Libro diario de ingresos y egresos de dinero físico en el punto de venta.
21. **`comisiones`:** Liquidaciones a favor del barbero generadas por cada servicio completado.

---

## 4. Pilares de Arquitectura Técnica

* **Inmutabilidad Contable:** Toda venta congela el precio y comisión del momento (`precio_historico`, `valor_comision`) para evitar que aumentos futuros de tarifas alteren cierres contables pasados.
* **Transaccionalidad Atómica:** Las operaciones de venta se ejecutan mediante `DB::transaction()` en un `VentaService`, garantizando que si falta stock de un perfume, la venta, la comisión y el movimiento de caja no se registren a medias.
* **Eliminación del problema N+1:** Consultas en Laravel optimizadas con Eager Loading (`with(['cliente', 'servicio', 'empleado'])`).
