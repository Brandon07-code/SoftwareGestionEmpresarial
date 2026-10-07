<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Rol;
use App\Models\User;
use App\Models\CodigoOtp;
use App\Models\Tercero;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cita;
use App\Models\TransaccionPago;
use App\Models\PuntosMovimiento;
use App\Models\CrmInteraccion;
use App\Models\InventarioMovimiento;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // 1. EMPRESAS MULTI-TENANT (Demostración de que el ERP no es mononegocio)
        // =====================================================================
        $empresaJyM = Empresa::create([
            'nit' => '901.884.231-1',
            'razon_social' => 'Barbería y Perfumería JyM S.A.S.',
            'nombre_comercial' => 'Barbería y Perfumería JyM',
            'tipo_negocio' => 'mixto', // Servicios estéticos + Retail mostrador
            'telefono' => '3146789012',
            'email' => 'gerencia@barberiajym.com',
            'direccion' => 'Carrera 4 # 11-25 Centro',
            'ciudad' => 'Cartago',
            'moneda' => 'COP',
            'puntos_por_monto' => 10000,
            'valor_por_punto' => 500,
            'activo' => true,
        ]);

        $sucursalPrincipal = Sucursal::create([
            'empresa_id' => $empresaJyM->id,
            'nombre' => 'Sede Principal Cartago Centro',
            'codigo' => 'SUC-01',
            'telefono' => '3146789012',
            'direccion' => 'Carrera 4 # 11-25',
            'activo' => true,
        ]);

        // Segunda empresa de giro completamente distinto: Distribuidora Mayorista
        $empresaDistribuidora = Empresa::create([
            'nit' => '900.542.119-4',
            'razon_social' => 'Distribuidora Comercial del Valle S.A.S.',
            'nombre_comercial' => 'Comercial del Valle',
            'tipo_negocio' => 'retail',
            'telefono' => '3109988776',
            'email' => 'contacto@comercialdelvalle.com',
            'direccion' => 'Avenida del Río # 15-40',
            'ciudad' => 'Pereira',
            'moneda' => 'COP',
            'puntos_por_monto' => 20000,
            'valor_por_punto' => 1000,
            'activo' => true,
        ]);

        $sucursalPereira = Sucursal::create([
            'empresa_id' => $empresaDistribuidora->id,
            'nombre' => 'Bodega Central Pereira',
            'codigo' => 'BOD-01',
            'telefono' => '3109988776',
            'direccion' => 'Avenida del Río # 15-40',
            'activo' => true,
        ]);

        // =====================================================================
        // 2. ROLES Y USUARIOS DEL SISTEMA (SPATIE PERMISSIONS)
        // =====================================================================
        $this->call(RolePermissionSeeder::class);

        $userAdmin = User::create([
            'empresa_id' => $empresaJyM->id,
            'name' => 'Brandon Cortés (Admin)',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'telefono' => '3128901234',
            'activo' => true,
        ]);
        $userAdmin->assignRole('admin');

        $userVendedor = User::create([
            'empresa_id' => $empresaJyM->id,
            'name' => 'Vendedor Prueba',
            'email' => 'vendedor@test.com',
            'password' => Hash::make('password'),
            'telefono' => '3187654321',
            'activo' => true,
        ]);
        $userVendedor->assignRole('vendedor');

        $userAlmacenista = User::create([
            'empresa_id' => $empresaJyM->id,
            'name' => 'Almacenista Prueba',
            'email' => 'almacenista@test.com',
            'password' => Hash::make('password'),
            'telefono' => '3145550011',
            'activo' => true,
        ]);
        $userAlmacenista->assignRole('almacenista');

        $userSinRol = User::create([
            'empresa_id' => $empresaJyM->id,
            'name' => 'Usuario Sin Rol',
            'email' => 'sinrol@test.com',
            'password' => Hash::make('password'),
            'telefono' => '3100000000',
            'activo' => true,
        ]);

        $userCajero = $userVendedor;
        $userBarbero = $userAlmacenista;

        // Código OTP de seguridad de prueba
        CodigoOtp::create([
            'user_id' => $userAdmin->id,
            'codigo' => '849201',
            'tipo' => 'login_2fa',
            'expira_at' => now()->addMinutes(15),
            'usado' => false,
        ]);

        // =====================================================================
        // 3. TERCEROS (PATRÓN PARTY MODEL: EMPLEADOS, PROVEEDORES Y CLIENTES)
        // =====================================================================
        
        // Empleados
        $empleadoCarlos = Tercero::create([
            'empresa_id' => $empresaJyM->id,
            'user_id' => $userBarbero->id,
            'tipo_documento' => 'CC',
            'numero_documento' => '1112450890',
            'nombre_completo' => 'Carlos Alberto Duque Osorio',
            'primer_nombre' => 'Carlos',
            'primer_apellido' => 'Duque',
            'telefono' => '3145550011',
            'whatsapp' => '3145550011',
            'email' => 'carlos@jym.com',
            'direccion' => 'Barrio San Jerónimo, Cartago',
            'ciudad' => 'Cartago',
            'es_cliente' => true, // También compra productos y se hace rituales (Actor Dual)
            'es_proveedor' => false,
            'es_empleado' => true,
            'cargo' => 'Barbero Máster & Educador',
            'porcentaje_comision' => 50.00,
            'activo' => true,
        ]);

        $empleadoAndres = Tercero::create([
            'empresa_id' => $empresaJyM->id,
            'tipo_documento' => 'CC',
            'numero_documento' => '1113882111',
            'nombre_completo' => 'Andrés Felipe Montoya',
            'primer_nombre' => 'Andrés',
            'primer_apellido' => 'Montoya',
            'telefono' => '3157778899',
            'email' => 'andres@jym.com',
            'direccion' => 'Barrio El Prado, Cartago',
            'ciudad' => 'Cartago',
            'es_cliente' => false,
            'es_proveedor' => false,
            'es_empleado' => true,
            'cargo' => 'Especialista en Barba & Spa',
            'porcentaje_comision' => 45.00,
            'activo' => true,
        ]);

        // Proveedores mayoristas
        $proveedorCosmeticos = Tercero::create([
            'empresa_id' => $empresaJyM->id,
            'tipo_documento' => 'NIT',
            'numero_documento' => '900887654-2',
            'nombre_completo' => 'Distribuidora Cosméticos del Eje S.A.S.',
            'telefono' => '3206654433',
            'email' => 'ventas@cosmeticosdeleje.com',
            'direccion' => 'Zona Industrial La Popa',
            'ciudad' => 'Dosquebradas',
            'es_cliente' => false,
            'es_proveedor' => true,
            'es_empleado' => false,
            'dias_credito' => 30,
            'limite_credito' => 5000000.00,
            'activo' => true,
        ]);

        // Clientes con CRM y Fidelización
        $clienteMateo = Tercero::create([
            'empresa_id' => $empresaJyM->id,
            'tipo_documento' => 'CC',
            'numero_documento' => '1112980123',
            'nombre_completo' => 'Mateo Gómez Pérez',
            'primer_nombre' => 'Mateo',
            'primer_apellido' => 'Gómez',
            'telefono' => '3114445566',
            'whatsapp' => '3114445566',
            'email' => 'mateo.gomez@gmail.com',
            'direccion' => 'Carrera 5 # 14-30',
            'ciudad' => 'Cartago',
            'es_cliente' => true,
            'es_proveedor' => false,
            'es_empleado' => false,
            'puntos_fidelidad' => 150,
            'activo' => true,
        ]);

        $clienteJuan = Tercero::create([
            'empresa_id' => $empresaJyM->id,
            'tipo_documento' => 'CC',
            'numero_documento' => '1115342901',
            'nombre_completo' => 'Juan David Henao',
            'primer_nombre' => 'Juan',
            'primer_apellido' => 'Henao',
            'telefono' => '3168889900',
            'whatsapp' => '3168889900',
            'email' => 'juandavid.h@gmail.com',
            'direccion' => 'Calle 10 # 3-12',
            'ciudad' => 'Cartago',
            'es_cliente' => true,
            'es_proveedor' => false,
            'es_empleado' => false,
            'puntos_fidelidad' => 80,
            'activo' => true,
        ]);

        $clienteSebastian = Tercero::create([
            'empresa_id' => $empresaJyM->id,
            'tipo_documento' => 'CC',
            'numero_documento' => '1114771229',
            'nombre_completo' => 'Sebastián Rivera',
            'primer_nombre' => 'Sebastián',
            'primer_apellido' => 'Rivera',
            'telefono' => '3171122334',
            'whatsapp' => '3171122334',
            'email' => 's.rivera@hotmail.com',
            'direccion' => 'Barrio Alamos',
            'ciudad' => 'Cartago',
            'es_cliente' => true,
            'es_proveedor' => false,
            'es_empleado' => false,
            'puntos_fidelidad' => 210,
            'activo' => true,
        ]);

        // =====================================================================
        // 4. CATÁLOGO: CATEGORÍAS, PRODUCTOS Y SERVICIOS
        // =====================================================================
        $catServBarberia = Categoria::create(['empresa_id' => $empresaJyM->id, 'nombre' => 'Barbería Tradicional', 'tipo' => 'SERVICIO', 'descripcion' => 'Cortes, degradados y estilos']);
        $catServSpa = Categoria::create(['empresa_id' => $empresaJyM->id, 'nombre' => 'Tratamientos & Spa', 'tipo' => 'SERVICIO', 'descripcion' => 'Ritual de barba y mascarillas']);
        
        $catProdPerfumes = Categoria::create(['empresa_id' => $empresaJyM->id, 'nombre' => 'Perfumería Masculina', 'tipo' => 'PRODUCTO', 'descripcion' => 'Fragancias importadas y contratipos']);
        $catProdCuidado = Categoria::create(['empresa_id' => $empresaJyM->id, 'nombre' => 'Cuidado Capilar & Barba', 'tipo' => 'PRODUCTO', 'descripcion' => 'Pomadas, ceras mate y aceites']);

        // Servicios
        $servCorte = Servicio::create([
            'empresa_id' => $empresaJyM->id,
            'categoria_id' => $catServBarberia->id,
            'codigo' => 'SRV-01',
            'nombre' => 'Corte Clásico & Fade Urbano',
            'descripcion' => 'Corte con máquina, tijera y peinado con cera mate',
            'precio_venta' => 25000.00,
            'duracion_minutos' => 40,
            'comision_base_porcentaje' => 50.00,
            'activo' => true,
        ]);

        $servRitualBarba = Servicio::create([
            'empresa_id' => $empresaJyM->id,
            'categoria_id' => $catServSpa->id,
            'codigo' => 'SRV-02',
            'nombre' => 'Ritual de Barba Spa',
            'descripcion' => 'Toalla caliente, perfilado con navaja y aceite ozonizado',
            'precio_venta' => 20000.00,
            'duracion_minutos' => 30,
            'comision_base_porcentaje' => 45.00,
            'activo' => true,
        ]);

        $servComboVip = Servicio::create([
            'empresa_id' => $empresaJyM->id,
            'categoria_id' => $catServSpa->id,
            'codigo' => 'SRV-03',
            'nombre' => 'Combo VIP JyM (Corte + Barba + Mascarilla)',
            'descripcion' => 'Experiencia completa de cuidado personal masculino',
            'precio_venta' => 45000.00,
            'duracion_minutos' => 75,
            'comision_base_porcentaje' => 50.00,
            'activo' => true,
        ]);

        // Productos de Mostrador
        $prodPerfumeBleu = Producto::create([
            'empresa_id' => $empresaJyM->id,
            'categoria_id' => $catProdPerfumes->id,
            'codigo_barras' => '770984123001',
            'nombre' => 'Loción Bleu Night 100ml',
            'descripcion' => 'Fragancia amaderada intensa para caballero',
            'precio_costo' => 55000.00,
            'precio_venta' => 110000.00, // 100% margen
            'stock_actual' => 15,
            'stock_minimo' => 4,
            'maneja_inventario' => true,
            'activo' => true,
        ]);

        $prodCeraMate = Producto::create([
            'empresa_id' => $empresaJyM->id,
            'categoria_id' => $catProdCuidado->id,
            'codigo_barras' => '770984123002',
            'nombre' => 'Cera Fijadora Efecto Mate 150g',
            'descripcion' => 'Fijación fuerte sin brillo a base de arcilla volcánica',
            'precio_costo' => 14000.00,
            'precio_venta' => 28000.00,
            'stock_actual' => 25,
            'stock_minimo' => 6,
            'maneja_inventario' => true,
            'activo' => true,
        ]);

        // Kárdex: Entrada inicial por compra
        InventarioMovimiento::create([
            'empresa_id' => $empresaJyM->id,
            'sucursal_id' => $sucursalPrincipal->id,
            'producto_id' => $prodPerfumeBleu->id,
            'user_id' => $userAdmin->id,
            'tipo_movimiento' => 'ENTRADA_COMPRA',
            'cantidad' => 15,
            'costo_unitario' => 55000.00,
            'stock_anterior' => 0,
            'stock_nuevo' => 15,
            'referencia_documento' => 'FACT-PROV-1044',
            'motivo' => 'Compra inicial a Distribuidora Cosméticos del Eje',
        ]);

        // =====================================================================
        // 5. CAJA Y ARQUEO
        // =====================================================================
        $cajaPrincipal = Caja::create([
            'empresa_id' => $empresaJyM->id,
            'sucursal_id' => $sucursalPrincipal->id,
            'nombre' => 'Caja Mostrador 01',
            'estado' => 'ABIERTA',
        ]);

        $sesionCaja = SesionCaja::create([
            'caja_id' => $cajaPrincipal->id,
            'user_id' => $userCajero->id,
            'monto_apertura' => 150000.00,
            'monto_cierre_efectivo' => null,
            'total_ventas_efectivo' => 25000.00,
            'total_ventas_digitales' => 110000.00,
            'diferencia' => 0.00,
            'estado' => 'ABIERTA',
            'observaciones' => 'Apertura de turno de la mañana con base completa',
            'abierto_at' => now()->startOfDay()->addHours(8),
            'cerrado_at' => null,
        ]);

        // =====================================================================
        // 6. VENTAS Y TRANSACCIONES CON PASARELA (WOMPI, NEQUI, EFECTIVO)
        // =====================================================================
        
        // Venta 1: Pagada vía WOMPI (Perfume Bleu)
        $ventaWompi = Venta::create([
            'empresa_id' => $empresaJyM->id,
            'sucursal_id' => $sucursalPrincipal->id,
            'sesion_caja_id' => $sesionCaja->id,
            'numero_factura' => 'FAC-0001',
            'tercero_cliente_id' => $clienteSebastian->id,
            'tercero_vendedor_id' => $empleadoCarlos->id,
            'subtotal' => 110000.00,
            'descuento_puntos' => 0.00,
            'impuesto_iva' => 0.00,
            'total' => 110000.00,
            'puntos_ganados' => 11, // $110.000 / $10.000 = 11 pts
            'puntos_canjeados' => 0,
            'estado' => 'PAGADA',
            'notas' => 'Pago realizado online mediante pasarela Wompi con tarjeta Bancolombia',
        ]);

        VentaDetalle::create([
            'venta_id' => $ventaWompi->id,
            'tipo_item' => 'PRODUCTO',
            'item_id' => $prodPerfumeBleu->id,
            'nombre_item' => $prodPerfumeBleu->nombre,
            'cantidad' => 1,
            'precio_unitario' => 110000.00,
            'descuento' => 0.00,
            'subtotal' => 110000.00,
            'costo_unitario' => 55000.00,
        ]);

        TransaccionPago::create([
            'empresa_id' => $empresaJyM->id,
            'venta_id' => $ventaWompi->id,
            'pasarela' => 'WOMPI',
            'referencia_interna' => 'WOMPI-TRX-' . strtoupper(bin2hex(random_bytes(4))),
            'referencia_pasarela' => 'WMP-9812739481',
            'monto' => 110000.00,
            'moneda' => 'COP',
            'estado' => 'APPROVED',
            'metodo_pago_detalle' => 'CARD_BANCOLOMBIA_VISA',
            'respuesta_payload' => [
                'status' => 'APPROVED',
                'amount_in_cents' => 11000000,
                'currency' => 'COP',
                'payment_method_type' => 'CARD',
                'authorization_code' => '049182',
            ],
            'url_checkout' => 'https://checkout.wompi.co/l/JYM-FAC-0001',
            'pagado_at' => now()->subMinutes(45),
        ]);

        // Libro Mayor de Puntos: Registro de la acumulación
        PuntosMovimiento::create([
            'empresa_id' => $empresaJyM->id,
            'tercero_id' => $clienteSebastian->id,
            'venta_id' => $ventaWompi->id,
            'tipo' => 'ACUMULACION',
            'puntos' => 11,
            'saldo_anterior' => 199,
            'saldo_nuevo' => 210,
            'motivo' => 'Compra Factura #FAC-0001 (Loción Bleu Night)',
        ]);

        // CRM Interacción
        CrmInteraccion::create([
            'empresa_id' => $empresaJyM->id,
            'tercero_id' => $clienteSebastian->id,
            'user_id' => $userCajero->id,
            'canal' => 'WHATSAPP',
            'tipo' => 'SEGUIMIENTO',
            'nota' => 'Cliente consultó disponibilidad de fragancia Bleu Night. Se envió enlace de pago Wompi y recogió en sede.',
            'fecha_contacto' => now()->subHours(2),
            'proximo_seguimiento' => now()->addDays(30),
        ]);

        // =====================================================================
        // 7. CITAS DEL DÍA (INTEGRADAS CON TERCEROS Y PASARELA NEQUI / EFECTIVO)
        // =====================================================================
        
        // Cita 1: Atendida y Pagada en Efectivo
        $citaCompletada = Cita::create([
            'empresa_id' => $empresaJyM->id,
            'sucursal_id' => $sucursalPrincipal->id,
            'tercero_cliente_id' => $clienteMateo->id,
            'tercero_especialista_id' => $empleadoCarlos->id,
            'servicio_id' => $servCorte->id,
            'fecha_hora' => now()->subHours(2),
            'total' => 25000.00,
            'estado' => 'COMPLETADA',
            'notas' => 'Cliente atendido puntualmente. Degradado alto con navaja.',
        ]);

        TransaccionPago::create([
            'empresa_id' => $empresaJyM->id,
            'cita_id' => $citaCompletada->id,
            'pasarela' => 'EFECTIVO',
            'referencia_interna' => 'CASH-TURNO-' . $citaCompletada->id,
            'monto' => 25000.00,
            'moneda' => 'COP',
            'estado' => 'APPROVED',
            'metodo_pago_detalle' => 'Efectivo en Caja Mostrador',
            'pagado_at' => now()->subHours(1)->subMinutes(20),
        ]);

        // Cita 2: En Atención con Cobro Pendiente por NEQUI QR
        $citaEnAtencion = Cita::create([
            'empresa_id' => $empresaJyM->id,
            'sucursal_id' => $sucursalPrincipal->id,
            'tercero_cliente_id' => $clienteJuan->id,
            'tercero_especialista_id' => $empleadoAndres->id,
            'servicio_id' => $servComboVip->id,
            'fecha_hora' => now()->addMinutes(10),
            'total' => 45000.00,
            'estado' => 'EN_ATENCION',
            'notas' => 'Combo VIP en curso. Pagará mediante escaneo Nequi QR dinámico.',
        ]);

        TransaccionPago::create([
            'empresa_id' => $empresaJyM->id,
            'cita_id' => $citaEnAtencion->id,
            'pasarela' => 'NEQUI',
            'referencia_interna' => 'NEQ-DYQR-' . strtoupper(bin2hex(random_bytes(3))),
            'monto' => 45000.00,
            'moneda' => 'COP',
            'estado' => 'PENDING',
            'metodo_pago_detalle' => 'QR Dinámico Nequi Push',
            'url_checkout' => 'https://recarga.nequi.com.co/jym-45000',
        ]);

        // Cita 3: Programada para la tarde
        Cita::create([
            'empresa_id' => $empresaJyM->id,
            'sucursal_id' => $sucursalPrincipal->id,
            'tercero_cliente_id' => $clienteSebastian->id,
            'tercero_especialista_id' => $empleadoCarlos->id,
            'servicio_id' => $servRitualBarba->id,
            'fecha_hora' => now()->addHours(3),
            'total' => 20000.00,
            'estado' => 'PROGRAMADA',
            'notas' => 'Reserva agendada por WhatsApp.',
        ]);

        // Seeder de Categorías y Productos para la práctica guiada
        $this->call(CategorySeeder::class);
    }
}
