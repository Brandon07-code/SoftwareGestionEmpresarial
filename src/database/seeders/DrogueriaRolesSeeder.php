<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DrogueriaRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definición de Permisos por Módulo y Acción
        $permissions = [
            // Módulo Productos
            'ver-productos',
            'crear-productos',
            'editar-productos',
            'eliminar-productos',

            // Módulo Lotes e Inventario
            'ver-lotes',
            'crear-lotes',
            'editar-lotes',
            'recibir-lotes',
            'dar-baja-lotes',

            // Módulo Compras
            'ver-compras',
            'crear-compras',
            'editar-compras',
            'anular-compras',

            // Módulo Ventas
            'ver-ventas',
            'crear-ventas',
            'editar-ventas',
            'anular-ventas',

            // Módulo Clientes
            'ver-clientes',
            'crear-clientes',
            'editar-clientes',

            // Módulo Proveedores
            'ver-proveedores',
            'crear-proveedores',
            'editar-proveedores',

            // Módulo Medicamentos de Control Especial (Regulatorio / Sanitario)
            'dispensar-control-especial',
            'auditar-recetas',

            // Módulo Reportes y Auditoría
            'ver-reportes-inventario',
            'ver-reportes-financieros',
            'ver-reportes-vencimientos',

            // Módulo Usuarios y Configuración
            'ver-usuarios',
            'gestionar-usuarios',
        ];

        // Crear permisos usando findOrCreate para idempotencia
        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // 2. Creación y asignación de Roles
        // Rol 1: Admin (Administrador / Dueño)
        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            'ver-lotes', 'dar-baja-lotes',
            'ver-compras', 'crear-compras', 'editar-compras', 'anular-compras',
            'ver-ventas', 'anular-ventas',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores',
            'ver-reportes-inventario', 'ver-reportes-financieros', 'ver-reportes-vencimientos',
            'ver-usuarios', 'gestionar-usuarios',
        ]);

        // Rol 2: Regente de Farmacia (Responsable técnico y sanitario)
        $regente = Role::findOrCreate('regente', 'web');
        $regente->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos',
            'ver-lotes', 'crear-lotes', 'editar-lotes', 'recibir-lotes', 'dar-baja-lotes',
            'ver-compras',
            'ver-ventas',
            'ver-clientes',
            'dispensar-control-especial', 'auditar-recetas',
            'ver-reportes-inventario', 'ver-reportes-vencimientos',
        ]);

        // Rol 3: Cajero (Ventas al público en punto de venta)
        $cajero = Role::findOrCreate('cajero', 'web');
        $cajero->syncPermissions([
            'ver-productos',
            'ver-ventas', 'crear-ventas',
            'ver-clientes', 'crear-clientes',
        ]);

        // Rol 4: Auxiliar de Bodega (Recepción física y acomodo de mercancía)
        $auxiliarBodega = Role::findOrCreate('auxiliar-bodega', 'web');
        $auxiliarBodega->syncPermissions([
            'ver-productos',
            'ver-lotes', 'recibir-lotes',
            'ver-compras',
            'ver-reportes-inventario',
        ]);
    }
}
