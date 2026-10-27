<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Crea (o actualiza) permisos y roles. Se puede ejecutar varias veces sin duplicar registros.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            'ver-categorias', 'crear-categorias', 'editar-categorias', 'eliminar-categorias',
            // Clientes
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            // Ventas
            'ver-ventas', 'crear-ventas', 'editar-ventas', 'eliminar-ventas',
            // Proveedores (Clase 10)
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores', 'eliminar-proveedores',
            // Compras (Clase 10: se agrega recibir-compras)
            'ver-compras', 'crear-compras', 'editar-compras', 'eliminar-compras', 'recibir-compras',
            // Reportes
            'ver-reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 1. Administrador: Control total sobre todos los módulos y acciones
        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // 2. Almacenista: Encargado de compras, proveedores, catálogo e inventario
        Role::findOrCreate('almacenista')->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos',
            'ver-categorias',
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores',
            'ver-compras', 'crear-compras', 'recibir-compras',
        ]);

        // 3. Cajero: Operador de caja y facturación POS en mostrador
        Role::findOrCreate('cajero')->syncPermissions([
            'ver-productos',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-ventas', 'crear-ventas',
        ]);

        // 4. Auditor: Control interno y supervisión (solo lectura de transacciones y reportes)
        Role::findOrCreate('auditor')->syncPermissions([
            'ver-productos',
            'ver-categorias',
            'ver-clientes',
            'ver-ventas',
            'ver-proveedores',
            'ver-compras',
            'ver-reportes',
        ]);

        // Rol complementario: Vendedor (atención y cotización)
        Role::findOrCreate('vendedor')->syncPermissions([
            'ver-productos',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-ventas', 'crear-ventas',
        ]);
    }
}
