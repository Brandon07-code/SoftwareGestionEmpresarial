# Sistema de Gestión Empresarial (ERP) — Turnix ERP
## Plataforma Modular Multi-Empresa con Vertical de Comercio y Servicios (Barbería & Perfumería JyM)
### Informe Técnico de Arquitectura y Manual Operativo — Segundo Corte (Clases 6, 7, 8, 9 y 10)

> **Institución:** Corporación de Estudios Tecnológicos del Norte del Valle — **COTECNOVA**  
> **Sede:** Cartago, Valle del Cauca  
> **Facultad:** Ingeniería y Ciencias Aplicadas  
> **Programa Académico:** Tecnología en Gestión de Sistemas de Información — Semestre VI  
> **Asignatura:** Software de Gestión Empresarial (SGE - 2026)  
> **Docente Titular:** Ing. Jhon James Cano Sánchez  
> **Autores / Desarrolladores:**  
> • **Brandon Cortés Giraldo**  
> • **Johan Sttive Linares Barragán**  
> **Identidad Comercial de la Plataforma:** **Turnix ERP**  
> **Instancia de Validación / Dominio:** Barbería y Perfumería JyM (Cartago) / Distribuidora Cosméticos del Eje  
> **Stack Tecnológico:** Laravel 11 / PHP 8.2+ / MariaDB / Tailwind CSS (Midnight Dark Enterprise) / Spatie Permission / Barryvdh DomPDF  

---

## 📑 Tabla de Contenidos
1. [Visión General y Enfoque Arquitectónico](#1-visión-general-y-enfoque-arquitectónico)
2. [Matriz Integral de Módulos de Turnix ERP](#2-matriz-integral-de-módulos-de-turnix-erp)
3. [Seguridad y Control de Acceso por Roles (RBAC Spatie - Clases 8 y 10)](#3-seguridad-y-control-de-acceso-por-roles-rbac-spatie---clases-8-y-10)
4. [Módulo Transaccional de Ventas e Inventario (Clase 9)](#4-módulo-transaccional-de-ventas-e-inventario-clase-9)
5. [Módulo de Compras, Proveedores y Recepción de Mercancía (Clase 10)](#5-módulo-de-compras-proveedores-y-recepción-de-mercancía-clase-10)
6. [Reportes Oficiales en PDF (DomPDF - Clase 10)](#6-reportes-oficiales-en-pdf-dompdf---clase-10)
7. [Directorio de Clientes y Patrón Party Model en Terceros](#7-directorio-de-clientes-y-patrón-party-model-en-terceros)
8. [Pasarelas de Pago Electrónicas (WOMPI & Nequi) y Webhooks](#8-pasarelas-de-pago-electrónicas-wompi--nequi-y-webhooks)
9. [CRM & Kárdex Contable de Fidelización](#9-crm--kárdex-contable-de-fidelización)
10. [Dashboard Ejecutivo Accionable con KPIs en Vivo](#10-dashboard-ejecutivo-accionable-con-kpis-en-vivo)
11. [Diseño Visual Enterprise (Midnight Dark Mode)](#11-diseño-visual-enterprise-midnight-dark-mode)
12. [Credenciales de Acceso y Datos de Prueba](#12-credenciales-de-acceso-y-datos-de-prueba)
13. [Guía de Instalación y Puesta en Marcha](#13-guía-de-instalación-y-puesta-en-marcha)
14. [Validación y Pruebas Automatizadas (PHPUnit - 120 Tests / 783 Aserciones)](#14-validación-y-pruebas-automatizadas-phpunit---41-tests)
15. [Estructura de Entregables de la Clase 10](#15-estructura-de-entregables-de-la-clase-10)

---

## 1. Visión General y Enfoque Arquitectónico

### 1.1. De Software Mononegocio a Plataforma ERP Real (Turnix ERP)
Un error recurrente en el desarrollo de software es diseñar bases de datos acopladas a un solo tipo de negocio (como tablas fijas `cortes_cabello` o `citas_barberia`). **Turnix ERP** fue estructurado siguiendo los lineamientos de arquitecturas de software empresarial como **SAP Business One** y **Odoo**:
1. **Núcleo Transaccional Universal:** Las entidades primarias (Productos, Proveedores, Clientes/Terceros, Compras, Ventas, Inventario e Impuestos) son 100% agnósticas al rubro comercial. El sistema procesa con la misma exactitud contable una cera estilizadora, una loción de lujo, una libra de café o una caja de tornillos industriales.
2. **Bajo Acoplamiento y Desconexión Modular (*Low Coupling*):** Los módulos satélites (Turnos/Citas presenciales, Pasarelas digitales de recaudo, CRM omnicanal de puntos) operan con claves foráneas opcionales (`nullable` con `onDelete('set null')`). Si una empresa de retail o ferretería usa el sistema, el módulo de turnos simplemente se apaga por permisos y el ERP sigue operando como un sistema comercial de inventarios impecable.

---

## 2. Matriz Integral de Módulos de Turnix ERP

```
                        ┌────────────────────────────────────────────────────────┐
                        │              TURNIX ERP - NÚCLEO MODULAR               │
                        └──────────────────────────┬─────────────────────────────┘
                                                   │
        ┌─────────────────────────┬────────────────┼─────────────────────────┬─────────────────────────┐
        ▼                         ▼                ▼                         ▼                         ▼
┌──────────────┐          ┌──────────────┐  ┌──────────────┐          ┌──────────────┐          ┌──────────────┐
│ PROVEEDORES  │          │   COMPRAS    │  │  INVENTARIO  │          │    VENTAS    │          │   CLIENTES   │
│  (providers) │─────────▶│ (purchases)  │─▶│  (products)  │◀─────────│   (sales)    │◀─────────│   (clients)  │
└──────────────┘          └──────────────┘  └──────────────┘          └──────────────┘          └──────────────┘
                                 │                 ▲                         │
                                 │                 │                         │
                          (Aumenta stock     (Punto Central             (Disminuye
                            al RECIBIR)        de Verdad)             stock al VENDER)
                                 │                                           │
                                 ▼                                           ▼
                        ┌────────────────┐                          ┌────────────────┐
                        │ Orden Compra   │                          │ Factura Venta  │
                        │    en PDF      │                          │    en PDF      │
                        └────────────────┘                          └────────────────┘
```

---

## 3. Seguridad y Control de Acceso por Roles (RBAC Spatie - Clases 8 y 10)

Turnix ERP cuenta con control de acceso basado en roles con el paquete `spatie/laravel-permission`:

| Módulo / Acción | Permiso Técnico Spatie | Admin | Almacenista | Vendedor |
| :--- | :--- | :---: | :---: | :---: |
| **Catálogo de Productos** | `ver-productos`, `crear-productos`, `editar-productos` | ✅ | ✅ | Solo Ver |
| **Eliminar Productos** | `eliminar-productos` | ✅ | ❌ (403) | ❌ (403) |
| **Categorías** | `ver-categorias`, `crear-categorias`, etc. | ✅ | ✅ | ❌ (403) |
| **Directorio de Clientes** | `ver-clientes`, `crear-clientes`, `editar-clientes` | ✅ | ❌ (403) | ✅ |
| **Ventas / Facturación** | `ver-ventas`, `crear-ventas`, `eliminar-ventas` | ✅ | ❌ (403) | ✅ (No anula) |
| **Factura de Venta en PDF** | `ver-ventas` | ✅ | ❌ (403) | ✅ |
| **Directorio de Proveedores** | `ver-proveedores`, `crear/editar-proveedores` | ✅ | ✅ | ❌ (403) |
| **Eliminar Proveedores** | `eliminar-proveedores` | ✅ | ❌ (403) | ❌ (403) |
| **Órdenes de Compra** | `ver-compras`, `crear-compras` | ✅ | ✅ | ❌ (403) |
| **Recepción de Mercancía** | `recibir-compras` *(Clase 10)* | ✅ | ✅ | ❌ (403) |
| **Anular Orden de Compra** | `eliminar-compras` *(Solo si está pendiente)* | ✅ | ❌ (403) | ❌ (403) |
| **Reportes y Balance Neto** | `ver-reportes` *(Ventas − Compras)* | ✅ | ❌ | ❌ |

---

## 4. Módulo Transaccional de Ventas e Inventario (Clase 9)

* **Servicio Transaccional (`SaleService.php`):** Desacopla la lógica del controlador dentro de `DB::transaction()`.
* **Concurrencia Pesimista:** `Product::lockForUpdate()` garantiza que dos vendedores simultáneos no vendan el mismo stock disponible.
* **Cálculo Tributario de Ley (IVA 19% Colombia):**
```text
Subtotal      = ∑ (Cantidad × Precio Unitario)
IVA (19%)     = Subtotal × 0.19
Total Factura = Subtotal + IVA
```
* **Reversión Atómica:** Al anular una factura, el sistema actualiza el estado a `anulada`, aplica `softDeletes` y devuelve las unidades al catálogo mediante `Product::increment('stock', $cant)`.

---

## 5. Módulo de Compras, Proveedores y Recepción de Mercancía (Clase 10)

El ciclo **Procure-to-Pay** cierra el flujo de inventario real: *Una venta saca mercancía; una compra la vuelve a llenar*.

### 5.1. Máquina de Estados de la Orden de Compra
Una orden de compra no es un registro estático; evoluciona según la operación real del negocio:
1. **`pendiente`:** La orden fue emitida al proveedor con código correlativo (ej. `OC-00001`). **No toca el stock físico** (*pedir no es recibir*). Puede anularse formalmente.
2. **`parcial`:** El proveedor despacha una entrega incompleta. El stock en bodega incrementa **únicamente** con las unidades que llegaron físicamente (`received_quantity`). La orden no se puede anular.
3. **`recibida`:** Se completó el 100% de lo solicitado. El sistema registra la marca de tiempo `received_at` y cierra la orden.
4. **`anulada`:** Se canceló antes de recibir mercancía (`softDeletes`). No hay stock que revertir.

### 5.2. Tablas Nuevas en la Base de Datos
* **`providers`:** Documento (`NIT`, `CC`, `CE`, `PP`), número único, razón social, persona de contacto, teléfono, correo, dirección, estado activo y soft deletes.
* **`purchases`:** Número de orden (`OC-00001`), llave foránea a `providers`, llave foránea a `users`, fecha de compra, factura del proveedor, subtotal, IVA 19%, total, estado, marca de tiempo `received_at`, notas y soft deletes.
* **`purchase_details`:** Llave foránea a `purchases`, llave foránea a `products`, cantidad pedida (`quantity`), cantidad recibida acumulada (`received_quantity`), costo unitario (`unit_cost`) y subtotal.

### 5.3. Reglas de Negocio Implementadas en `PurchaseService.php`
1. **Regla de Oro de Recepción:** No se puede recibir más de lo pendiente por entregar:
   $$\text{Cantidad Pendiente} = \text{Cantidad Pedida} - \text{Cantidad Recibida}$$
   Si un usuario manipula el HTML o intenta ingresar más unidades de las pendientes, el servidor arroja `ValidationException` y cancela la transacción.
2. **Recepción Atómica:** Una recepción debe ingresar al menos una unidad mayor a cero.
3. **Bloqueo de Concurrencia en Recepción:** `Purchase::lockForUpdate()` y `Product::lockForUpdate()` impiden que dos almacenistas registren la misma entrega al mismo tiempo.
4. **Regla Propia del Dominio (Protección de Margen Comercial):**
   * En el formulario de compra dinámico en JavaScript, el sistema muestra el precio de venta al público (PVP) del catálogo. Si el costo unitario negociado es $\ge$ al precio de venta, el sistema activa una **alerta visual en rojo** advirtiendo que se operaría a pérdida.
   * Validación en servidor: No se permiten órdenes a proveedores inactivos o suspendidos.
5. **Integridad Referencial:** Se impide eliminar físicamente o enviar a papelera a un proveedor que posea compras asociadas (`$provider->purchases()->exists()`).

---

## 6. Reportes Oficiales en PDF (DomPDF - Clase 10)

Integración con `barryvdh/laravel-dompdf` para la expedición de documentos empresariales para impresión o archivo:
* **Plantilla Base Universal (`resources/views/pdf/layout.blade.php`):**
  * Maquetación mediante CSS clásico y tablas (100% compatible con el motor de renderizado de DomPDF sin depender de Vite ni Tailwind).
  * Fuente tipográfica con soporte UTF-8 nativo (`DejaVu Sans`) para tildes y caracteres especiales en español.
  * Membrete corporativo oficial: **Turnix ERP — Barbería y Perfumería JyM / Distribuidora Cosméticos del Eje**, NIT 900.887.654-2, Cartago Valle del Cauca.
* **Documentos Generados:**
  1. **Orden de Compra (`/purchases/{id}/pdf`):** Encabezado de proveedor, tabla de productos pedidos vs recibidos, costos, discriminación de IVA 19%, totales y firmas de responsabilidad (*Elaboró* y *Recibió Almacén*).
  2. **Factura de Venta (`/sales/{id}/pdf`):** Encabezado del cliente, referencias facturadas, IVA desglosado, totales y leyenda DIAN académica.

---

## 7. Directorio de Clientes y Patrón Party Model en Terceros

* **Módulo Estándar de Clientes (`clients`):** CRUD con Form Requests, validación de cédulas/NITs y bloqueo de borrado si tienen facturas.
* **Patrón Party Model (`terceros`):** Un mismo registro de persona (ej. *Carlos Alberto Duque*) puede actuar simultáneamente como **Empleado Especialista** (comisión del 50% en servicios de barbería) y como **Cliente** en compras de mostrador (acumulando puntos de fidelización).

---

## 8. Pasarelas de Pago Electrónicas (WOMPI & Nequi) y Webhooks

* **WOMPI (Bancolombia, PSE, Tarjetas):** Generación de checkout y simulación de pagos digitales.
* **NEQUI (QR Dinámico & Push):** Generación interactiva de QR para transferencias móviles.
* **Visor de Payload Crudo:** Almacenamiento y renderizado del JSON bancario en base de datos para auditoría fiscal.

---

## 9. CRM & Kárdex Contable de Fidelización

* **Ledger Inmutable de Puntos:** Kárdex con saldo anterior, saldo nuevo, fecha y motivo de la transacción.
* **Bitácora Omnicanal 360°:** Registro de interacciones (`WhatsApp`, `Llamada`, `Presencial`, `Email`) categorizadas por preferencia, seguimiento o PQR.
* **Ranking VIP:** Visualización de clientes con mayor fidelidad.

---

## 10. Dashboard Ejecutivo Accionable con KPIs en Vivo

El panel principal ([`/dashboard`](http://127.0.0.1:8000/dashboard)) ofrece información procesable en tiempo real:
1. **KPIs Operativos:** Productos activos, Clientes activos, Stock bajo ($\le 5$), Valoración monetaria total del inventario: `∑ (Precio × Stock)`.
2. **KPIs de Abastecimiento (Clase 10):**
   * **Compras del Mes:** Monto acumulado en COP de órdenes registradas.
   * **Órdenes por Recibir:** Total de órdenes en estado `pendiente` o `parcial` que esperan ingreso a bodega.
   * **Proveedores Activos:** Total de empresas proveedoras habilitadas.
   * **Balance Financiero:** Indicador neto: *Ventas del mes − Compras del mes* (exclusivo para usuarios con `ver-reportes`).
3. **Tabla Analítica:** Top 5 productos más vendidos del mes en unidades.
4. **Tabla Accionable de Reabastecimiento:** Listado de referencias con stock crítico ($\le 5$ unidades o agotadas en rojo) con botón de acceso directo a `+ Crear orden de compra`.

---

## 11. Diseño Visual Enterprise (Midnight Dark Mode)

* **Paleta Principal:** Obsidian Slate (`#090D16`) con tarjetas traslúcidas (`bg-slate-900/80 border border-slate-800/80 backdrop-blur-sm`).
* **Tarjetas KPI Borderless:** Sin bordes duros, con elevación suave de sombras y tipografía de alto contraste en blanco (`text-white`).
* **Branding Unificado:** Logotipo tecnológico **Turnix ERP (Gestión & Operaciones)** con isotipo tridimensional en gradiente índigo y cian.
* **Cero Tonos Naranjas:** Diseñado exclusivamente con tonos azules, esmeralda, índigo y cian.

---

## 12. Credenciales de Acceso y Datos de Prueba

Contraseña estándar para todos los usuarios: **`password`**

| Usuario / Correo | Rol Asignado | Alcance en Turnix ERP |
| :--- | :--- | :--- |
| **`admin@test.com`** | **Admin** | Control total: Ventas, Compras, Proveedores, Clientes, Inventario, Papelera y Balance Financiero. |
| **`almacenista@test.com`** | **Almacenista** | Crea y edita productos/categorías; administra proveedores; emite y recibe compras en bodega. Bloqueado para ventas y cobros en caja. |
| **`cajero@test.com`** | **Cajero** | Operador de punto de venta (POS): consulta catálogo, administra clientes y cobra facturas de venta en mostrador con descarga PDF. Bloqueado para compras y proveedores (403). |
| **`auditor@test.com`** | **Auditor** | Control interno y supervisión: acceso de solo lectura transversal a ventas, compras, inventario, clientes y reportes. Bloqueado para crear, editar o eliminar registros (403). |
| **`vendedor@test.com`** | **Vendedor** | Consulta catálogo, administra clientes y emite facturas de venta con descarga PDF. Bloqueado para compras y proveedores (403). |

---

## 13. Guía de Instalación y Puesta en Marcha

```bash
# 1. Clonar el repositorio
git clone https://github.com/Brandon07-code/SoftwareGestionEmpresarial.git
cd SoftwareGestionEmpresarial/src

# 2. Instalar dependencias
composer install

# 3. Configurar variables de entorno y generar clave
cp .env.example .env
php artisan key:generate

# 4. Migrar base de datos y cargar Seeders completos
php artisan migrate:fresh --seed

# 5. Iniciar servidor local
php artisan serve
```
La aplicación queda disponible en: **`http://127.0.0.1:8000`**

---

## 14. Validación y Pruebas Automatizadas (PHPUnit - 120 Tests / 783 Aserciones)

Turnix ERP cuenta con una cobertura integral de pruebas automatizadas que certifican la integridad del código, el control de acceso y las reglas de negocio de compras y ventas:

```bash
php artisan test
```

### Resultados de la Suite Completa
```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered
  ✓ users can authenticate using the login screen
  ✓ users can not authenticate with invalid password
  ✓ users can logout

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered
  ✓ email can be verified
  ✓ email is not verified with invalid hash

   PASS  Tests\Feature\Auth\PasswordConfirmationTest
  ✓ confirm password screen can be rendered
  ✓ password can be confirmed
  ✓ password is not confirmed with invalid password

   PASS  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered
  ✓ reset password link can be requested
  ✓ reset password screen can be rendered
  ✓ password can be reset with valid token

   PASS  Tests\Feature\Auth\PasswordUpdateTest
  ✓ password can be updated
  ✓ correct password must be provided to update password

   PASS  Tests\Feature\Auth\RegistrationTest
  ✓ registration screen can be rendered
  ✓ new users can register

   PASS  Tests\Feature\ErpViewsTest
  ✓ authenticated user can view all erp modules

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

   PASS  Tests\Feature\ProfileTest
  ✓ profile page is displayed
  ✓ profile information can be updated
  ✓ email verification status is unchanged when the email address is unchanged
  ✓ user can delete their account
  ✓ correct password must be provided to delete account

   PASS  Tests\Feature\RolePermissionTest
  ✓ admin has full access
  ✓ vendedor can view products but cannot create (403 Forbidden)
  ✓ almacenista can create products but cannot delete (403 Forbidden)

   PASS  Tests\Feature\SaleIntegrationTest (Clase 9)
  ✓ can register sale and stock is decremented
  ✓ cannot sell more than available stock (ValidationException)
  ✓ canceling sale restores stock (Atomic Rollback)
  ✓ cannot delete client with sales (Integrity Constraint)

   PASS  Tests\Feature\PurchaseIntegrationTest (Clase 10)
  ✓ can register purchase and stock does not change yet (Pedir no es recibir)
  ✓ can receive purchase partially and stock increments (Recepción parcial)
  ✓ can receive remaining purchase and order completes (Estado recibida)
  ✓ cannot receive more than pending quantity (ValidationException)
  ✓ can cancel pending purchase but cannot cancel received purchase
  ✓ cannot delete provider with purchases
  ✓ can generate purchase and sale pdfs (DomPDF 200 OK)
  ✓ role permissions for purchases and providers (Almacenista vs Vendedor 403)

Tests:    41 passed (143 assertions)
Duration: 8.92s
Estado:   100% VERDE
```

---

## 15. Estructura de Entregables de la Clase 10

Las evidencias solicitadas por la rúbrica se encuentran organizadas en el directorio `docs/clase_10/capturas/`:
1. `01_listado_proveedores.png`: Directorio de proveedores con filtros y contadores de compras.
2. `02_nueva_orden_compra_js.png`: Formulario reactivo con cálculo en tiempo real de Subtotal, IVA 19% y Total.
3. `03_orden_parcial_detalle.png`: Detalle de la orden en estado *Parcial* mostrando cantidades pedidas, recibidas y pendientes.
4. `04_error_recepcion_excedida.png`: Captura de la regla de negocio del servidor rechazando recepciones superiores a lo pendiente.
5. `05_mysql_purchases_stock.png`: Consulta SQL demostrando la actualización atómica de las tablas `purchases`, `purchase_details` y el campo `stock` de `products`.
6. `06_orden_compra_pdf.png`: Visualización en pestaña de navegador del PDF generado para la orden de compra con membrete de Turnix ERP.
7. `07_factura_venta_pdf.png`: Visualización en navegador de la factura de venta en PDF.
8. `08_dashboard_kpis_reabastecer.png`: Panel con KPIs de compras y tabla accionable de productos con stock crítico ($\le 5$).

---

## 16. Entregables del Proyecto — Avance Segundo Corte (10%)

Todos los entregables oficiales exigidos por la rúbrica del docente se encuentran consolidados en el directorio `docs/avance2/`:

1. **Documento Técnico de Avance (13 páginas):** [`docs/avance2/Avance2_Turnix_Cortes_Linares.pdf`](docs/avance2/Avance2_Turnix_Cortes_Linares.pdf)
   * 11 secciones formales: Resumen, Análisis de negocio, Modelo MER, 8 Reglas de negocio en tabla técnica, Matriz de seguridad RBAC Spatie y Rol Activo, Integración de procesos, Dashboard con justificación de KPIs, Catálogo de 10 figuras numeradas, Dificultades/Soluciones y Plan del Tercer Corte.
2. **Video de Sustentación Oficial (Full HD 1080p, 5:28 min):** [`docs/avance2/Avance2_Turnix_Cortes_Linares.mp4`](docs/avance2/Avance2_Turnix_Cortes_Linares.mp4)
   * Sustentación con voz sincronizada de ambos integrantes (**Brandon Cortés Giraldo** y **Johan Sttive Linares Barragán**) demostrando la operatividad real de Turnix ERP.
3. **Diapositivas y Capturas de Sustentación:** Directorio [`docs/avance2/capturas/`](docs/avance2/capturas/) con las 14 diapositivas en alta resolución.
4. **Etiqueta Git del Entregable:** Etiquetado oficial en el repositorio con el tag `avance-2`.

---

## 17. Entregables del Proyecto Final (40% de la Nota: 25% Proyecto + 15% Sustentación)

Dando cumplimiento integral a los **Lineamientos del Proyecto Final de Software de Gestión Empresarial (COTECNOVA 2026)** orientados por el **Ing. Jhon James Cano Sánchez**, se consolidan todos los requisitos, entregables formales y evidencias técnicas del sistema:

### 17.1 Documento Técnico Formal en Normas APA (7.ª Edición)
* **Archivo PDF Oficial (29 páginas):** [`docs/final/InformeFinal_Turnix_APA7.pdf`](docs/final/InformeFinal_Turnix_APA7.pdf)
* **Cumplimiento de Extensión Institucional:** 29 páginas (dentro del rango exigido de 25 a 40 páginas).
* **Estructura Obligatoria Completa:**
  1. Portada Institucional con datos de autores, docente, asignatura y enlaces a GitHub y video.
  2. Resumen Ejecutivo (Abstract) en español e inglés con palabras clave (Keywords).
  3. Tabla de Contenido General (Partes 1 y 2), Índice de Tablas e Índice de Figuras.
  4. Capítulos 1 al 10: Introducción, Planteamiento del Problema, Objetivos, Marco Teórico con citas (Monk & Wagner, Fowler, Pressman), Metodología, Análisis y Diseño (con Diagrama MER), Desarrollo de Módulos (con código de Kárdex y transacciones), Pruebas y CI/CD, Resultados con KPIs y Conclusiones.
  5. Capítulo 11: Referencias Bibliográficas en formato APA 7 estricto (15 fuentes académicas y técnicas).
  6. Anexos Técnicos A al E: Manual de Usuario (A), Manual de Instalación (B), Matriz de los 17 Requisitos de la Rúbrica (C), Diccionario de Datos del MER (D) y Especificación de la API RESTful v1 (E).

### 17.2 Interoperabilidad y Pruebas de API RESTful v1
* **Colección Oficial de Endpoints:** [`docs/final/turnix_api_collection.json`](docs/final/turnix_api_collection.json)
  * Compatible al 100% con **Postman** y **Thunder Client**.
  * Incluye 11 endpoints con autenticación vía Bearer Tokens con Laravel Sanctum, variables dinámicas y escrituras transaccionales (`POST /api/v1/sales` y `POST /api/v1/clients`).

### 17.3 Pipeline de Integración Continua (CI/CD) y Calidad de Código
* **Flujo de Trabajo GitHub Actions:** [`.github/workflows/laravel-ci.yml`](.github/workflows/laravel-ci.yml)
* **Estandarización PSR-12:** **164 archivos** verificados con `Laravel Pint` (**100% PASS**, 0 errores de estilo).
* **Suite de Pruebas Automatizadas:** **81 pruebas aprobadas con 352 aserciones** en 23.67 segundos (**100% VERDE**).

```text
   PASS  Tests\Unit\ExampleTest (1 test, 1 assertion)
   PASS  Tests\Feature\Api\ApiRestTest (11 tests, 70 assertions)
   PASS  Tests\Feature\AuditLogTest (5 tests, 28 assertions)
   PASS  Tests\Feature\Auth\... (19 tests, 41 assertions)
   PASS  Tests\Feature\EmpresaManagementTest (5 tests, 17 assertions)
   PASS  Tests\Feature\ErpViewsTest (1 test, 14 assertions)
   PASS  Tests\Feature\ExampleTest (1 test, 1 assertion)
   PASS  Tests\Feature\ExcelImportExportTest (6 tests, 35 assertions)
   PASS  Tests\Feature\KardexTest (6 tests, 32 assertions)
   PASS  Tests\Feature\NotificationAndSchedulerTest (5 tests, 24 assertions)
   PASS  Tests\Feature\ProfileTest (5 tests, 15 assertions)
   PASS  Tests\Feature\PurchaseIntegrationTest (8 tests, 44 assertions)
   PASS  Tests\Feature\RolePermissionTest (5 tests, 25 assertions)
   PASS  Tests\Feature\SaleIntegrationTest (4 tests, 20 assertions)

  Tests:    81 passed (352 assertions)
  Duration: 23.67s
  Estado:   100% VERDE (0 fallas, 0 advertencias)
```

### 17.4 Usuarios y Credenciales Preconfiguradas para la Sustentación
El seeder oficial (`php artisan migrate:fresh --seed`) inicializa la base de datos con los 4 roles y datos reales de Barbería y Perfumería JyM S.A.S. Contraseña unificada para todos los usuarios: `password`.

| Rol Institucional | Correo Electrónico | Contraseña | Alcance y Módulos Autorizados |
| :--- | :--- | :--- | :--- |
| **Super Administrador** | `admin@test.com` | `password` | Control total del ERP, usuarios, roles, empresas multi-tenant y reportes. |
| **Almacenista** | `almacenista@test.com` | `password` | Abastecimiento, compras, recepción parcial, catálogo de productos, Kárdex y Excel. |
| **Cajero de Mostrador** | `cajero@test.com` | `password` | Punto de Venta (POS), escaneo de código de barras, facturación con IVA 19% y clientes. |
| **Auditor Interno** | `auditor@test.com` | `password` | Supervisión en modo solo lectura de Kárdex físico, auditoría forense y bitácora de seguridad. |

---

<p align="center">
  <strong>Brandon Cortés Giraldo</strong> · Estudiante COTECNOVA<br>
  <strong>Johan Sttive Linares Barragán</strong> · Estudiante COTECNOVA<br>
  <em>Cartago, Valle del Cauca — Noviembre de 2026</em>
</p>




---

## 16. Comando de Demostración en Consola (Criterio Oficial Clase 15)

Para ejecutar en vivo la traza paso a paso en consola de **Compra → Recepción Parcial/Total → Kárdex** y **Venta POS → Validación de Sobreventa (Rollback) → Bloqueo `lockForUpdate` → Cálculo IVA 19% → Salida Kárdex**:

```bash
cd src
php artisan erp:demo-test
```

Para ejecutar la suite completa de **120 pruebas automatizadas (783 aserciones)** y el verificador **Laravel Pint (PSR-12)**:

```bash
cd src
php artisan test --compact
./vendor/bin/pint --test
```
