<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthAndConfigModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
    }

    public function test_inactive_user_cannot_login_via_web_and_logs_security_event(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();
        $vendedor->update(['activo' => false]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'vendedor@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Su usuario está inactivo', session('errors')->first('email'));
        $this->assertGuest();

        $this->assertDatabaseMissing('activity_log', [
            'log_name' => 'seguridad',
            'causer_id' => $vendedor->id,
            'description' => 'Inicio de sesión exitoso',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'seguridad',
            'causer_id' => $vendedor->id,
            'description' => 'Intento de inicio de sesión bloqueado: usuario inactivo',
        ]);
    }

    public function test_single_role_and_no_role_login_flow_to_dashboard(): void
    {
        $this->post(route('login'), [
            'email' => 'vendedor@test.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertEquals('vendedor', session('active_role'));
        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => 'sinrol@test.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertNull(session('active_role'));
    }

    public function test_multi_role_login_requires_role_selection_and_strictly_scopes_permissions(): void
    {
        $supervisor = User::where('email', 'supervisor@test.com')->first();
        $this->assertNotNull($supervisor);
        $this->assertTrue($supervisor->hasMultipleRoles());
        $this->assertFalse($supervisor->hasPermissionInActiveRole('ver-compras'));

        $this->post(route('login'), [
            'email' => 'supervisor@test.com',
            'password' => 'password',
        ])->assertRedirect(route('role.select'));

        $this->assertAuthenticatedAs($supervisor);
        $this->assertNull(session('active_role'));

        // Middleware active.role redirige a seleccionar-rol hasta elegir rol activo
        $this->get(route('dashboard'))->assertRedirect(route('role.select'));
        $this->get(route('products.index'))->assertRedirect(route('role.select'));

        // Seleccionar vendedor
        $this->post(route('role.store'), ['role' => 'vendedor'])
            ->assertRedirect(route('dashboard', absolute: false));
        $this->assertEquals('vendedor', session('active_role'));

        $this->get(route('sales.create'))->assertOk();
        $this->get(route('purchases.create'))->assertForbidden();

        // Cambiar a almacenista desde el selector de barra
        $this->post(route('role.switch'), ['role' => 'almacenista'])
            ->assertRedirect(route('dashboard'));
        $this->assertEquals('almacenista', session('active_role'));

        $this->get(route('purchases.create'))->assertOk();
        $this->get(route('sales.create'))->assertForbidden();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'seguridad',
            'causer_id' => $supervisor->id,
            'description' => 'Cambio de rol activo de sesión',
        ]);
    }

    public function test_dual_role_admin_switching_to_secondary_role_respects_active_role(): void
    {
        $dualAdmin = User::factory()->create([
            'email' => 'dualadmin@test.com',
            'activo' => true,
        ]);
        $dualAdmin->syncRoles(['admin', 'vendedor']);

        $this->actingAs($dualAdmin)
            ->withSession(['active_role' => 'vendedor'])
            ->get(route('configuracion.index'))
            ->assertForbidden();

        $dashboard = $this->actingAs($dualAdmin)
            ->withSession(['active_role' => 'vendedor'])
            ->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertDontSee(route('empresas.index'));
    }

    public function test_non_admin_roles_cannot_access_configuration_module(): void
    {
        foreach (['vendedor@test.com', 'almacenista@test.com', 'cajero@test.com', 'auditor@test.com', 'sinrol@test.com'] as $email) {
            $user = User::where('email', $email)->first();

            $this->actingAs($user)->get(route('configuracion.index'))->assertForbidden();
            $this->actingAs($user)->get(route('configuracion.users.index'))->assertForbidden();
            $this->actingAs($user)->get(route('configuracion.matrix'))->assertForbidden();
        }
    }

    public function test_admin_can_create_edit_toggle_users_and_audit_renders_safely(): void
    {
        $empresa = Empresa::first();

        $createResponse = $this->actingAs($this->admin)->post(route('configuracion.users.store'), [
            'name' => 'Operador Multirrol',
            'email' => 'operador.nuevo@test.com',
            'telefono' => '3101112233',
            'empresa_id' => $empresa->id,
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'activo' => '1',
            'roles' => ['vendedor', 'cajero'],
            'permissions' => ['ver-reportes'],
        ]);

        $createResponse->assertRedirect(route('configuracion.users.index'));

        $created = User::where('email', 'operador.nuevo@test.com')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->activo);
        $this->assertTrue($created->hasAllRoles(['vendedor', 'cajero']));
        $this->assertTrue($created->hasDirectPermission('ver-reportes'));

        // Desactivar vía toggle
        $this->actingAs($this->admin)
            ->patch(route('configuracion.users.toggle', $created))
            ->assertRedirect(route('configuracion.users.index'));
        $this->assertFalse($created->fresh()->activo);

        // Bitácora de auditoría debe renderizar diffs con arrays sin error 500
        $this->actingAs($this->admin)
            ->get(route('audit.index', ['log_name' => 'seguridad']))
            ->assertOk()
            ->assertSee('Operador Multirrol');
    }

    public function test_access_control_protection_rules_block_self_lockout_and_last_admin_removal(): void
    {
        // 1. No puede desactivarse a sí mismo (toggle)
        $this->actingAs($this->admin)
            ->patch(route('configuracion.users.toggle', $this->admin))
            ->assertSessionHasErrors('activo');
        $this->assertTrue($this->admin->fresh()->activo);

        // 2. No puede quitarse el rol admin a sí mismo
        $this->actingAs($this->admin)
            ->put(route('configuracion.users.update', $this->admin), [
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'activo' => '1',
                'roles' => ['vendedor'],
            ])
            ->assertSessionHasErrors('roles');
        $this->assertTrue($this->admin->fresh()->hasRole('admin'));

        // 3. Un segundo admin no puede desactivar ni quitar rol admin al último otro admin activo si solo queda él -> crear segundo admin y probar sobre el primero mientras el segundo está inactivo o al revés
        $secondAdmin = User::factory()->create(['activo' => true]);
        $secondAdmin->assignRole('admin');
        $this->admin->update(['activo' => false]);

        // Ahora secondAdmin es el único admin activo; intentando desactivarse falla
        $this->actingAs($secondAdmin)
            ->patch(route('configuracion.users.toggle', $secondAdmin))
            ->assertSessionHasErrors('activo');

        // 4. No puede eliminar el rol admin ni un rol con usuarios asignados
        $adminRole = Role::where('name', 'admin')->first();
        $vendedorRole = Role::where('name', 'vendedor')->first();

        $this->actingAs($secondAdmin)
            ->delete(route('configuracion.roles.destroy', $adminRole))
            ->assertSessionHasErrors('role');

        $this->actingAs($secondAdmin)
            ->delete(route('configuracion.roles.destroy', $vendedorRole))
            ->assertSessionHasErrors('role');
    }

    public function test_matrix_update_and_custom_permission_lifecycle(): void
    {
        $vendedorRole = Role::where('name', 'vendedor')->first();
        $adminRole = Role::where('name', 'admin')->first();

        // Actualizar matriz dando 'ver-reportes' a vendedor (y tratando de omitir admin)
        $this->actingAs($this->admin)
            ->put(route('configuracion.matrix.update'), [
                'matrix' => [
                    $vendedorRole->id => ['ver-productos', 'ver-reportes'],
                    $adminRole->id => [],
                ],
            ])
            ->assertRedirect(route('configuracion.matrix'));

        $this->assertTrue($vendedorRole->fresh()->hasPermissionTo('ver-reportes'));
        $this->assertEquals(Permission::count(), $adminRole->fresh()->permissions()->count());

        // Crear permiso personalizado
        $this->actingAs($this->admin)
            ->post(route('configuracion.permissions.store'), ['name' => 'aprobar-descuentos'])
            ->assertRedirect(route('configuracion.matrix'));

        $perm = Permission::where('name', 'aprobar-descuentos')->first();
        $this->assertNotNull($perm);
        $this->assertTrue($adminRole->fresh()->hasPermissionTo('aprobar-descuentos'));

        // Asignar a vendedor: no se debe poder eliminar mientras esté en uso
        $vendedorRole->givePermissionTo($perm);
        $this->actingAs($this->admin)
            ->delete(route('configuracion.permissions.destroy', $perm))
            ->assertSessionHasErrors('permission');
        $this->assertDatabaseHas('permissions', ['name' => 'aprobar-descuentos']);

        // Quitar de vendedor y eliminar exitosamente
        $vendedorRole->revokePermissionTo($perm);
        $this->actingAs($this->admin)
            ->delete(route('configuracion.permissions.destroy', $perm))
            ->assertRedirect(route('configuracion.matrix'));
        $this->assertDatabaseMissing('permissions', ['name' => 'aprobar-descuentos']);

        $this->assertTrue(Activity::where('log_name', 'seguridad')->exists());
    }

    public function test_dual_role_admin_cannot_access_empresas_when_active_role_is_vendedor(): void
    {
        $dualAdmin = User::factory()->create([
            'email' => 'dualadmin.empresas@test.com',
            'activo' => true,
        ]);
        $dualAdmin->syncRoles(['admin', 'vendedor']);

        $this->actingAs($dualAdmin)
            ->withSession(['active_role' => 'vendedor'])
            ->get(route('empresas.index'))
            ->assertForbidden();

        $this->actingAs($dualAdmin)
            ->withSession(['active_role' => 'vendedor'])
            ->get(route('empresas.create'))
            ->assertForbidden();

        $this->actingAs($dualAdmin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('empresas.index'))
            ->assertOk();

        $this->actingAs($dualAdmin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('empresas.create'))
            ->assertOk();
    }

    public function test_core_system_permissions_cannot_be_deleted_even_when_admin_only(): void
    {
        foreach (['gestionar-usuarios', 'eliminar-productos'] as $coreName) {
            $corePerm = Permission::where('name', $coreName)->firstOrFail();

            $this->actingAs($this->admin)
                ->from(route('configuracion.matrix'))
                ->delete(route('configuracion.permissions.destroy', $corePerm))
                ->assertRedirect(route('configuracion.matrix'))
                ->assertSessionHasErrors('permission');

            $this->assertDatabaseHas('permissions', ['name' => $coreName]);
        }

        // La vista de matriz muestra insignia Sistema y no habilita eliminación sobre permisos núcleo
        $matrixResponse = $this->actingAs($this->admin)->get(route('configuracion.matrix'));
        $matrixResponse->assertOk();
        $matrixResponse->assertSee('Sistema');

        // Un permiso personalizado sin uso aún puede crearse y eliminarse libremente
        $this->actingAs($this->admin)
            ->post(route('configuracion.permissions.store'), ['name' => 'permiso-temporal-m1'])
            ->assertRedirect(route('configuracion.matrix'));

        $customPerm = Permission::where('name', 'permiso-temporal-m1')->firstOrFail();

        $this->actingAs($this->admin)
            ->delete(route('configuracion.permissions.destroy', $customPerm))
            ->assertRedirect(route('configuracion.matrix'));

        $this->assertDatabaseMissing('permissions', ['name' => 'permiso-temporal-m1']);
    }
}
