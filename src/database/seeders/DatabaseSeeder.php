<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Cita;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Clientes de prueba (Cartago, Valle del Cauca)
        $clientes = [
            ['nombre' => 'Carlos Alberto Restrepo', 'telefono' => '3124567890', 'email' => 'carlos.restrepo@gmail.com', 'direccion' => 'Barrio La Pradera, Calle 10 # 4-25', 'puntos_fidelizacion' => 45, 'notas' => 'Prefiere corte con tijera.'],
            ['nombre' => 'María Elena Sánchez', 'telefono' => '3187654321', 'email' => 'maria.sanchez@hotmail.com', 'direccion' => 'Centro, Cra 5 # 12-30', 'puntos_fidelizacion' => 80, 'notas' => 'Alérgica a tintes con amoniaco.'],
            ['nombre' => 'Juan Diego Morales', 'telefono' => '3156789012', 'email' => 'juan.morales@outlook.com', 'direccion' => 'Barrio El Jardín, Mz 4 Casa 12', 'puntos_fidelizacion' => 20, 'notas' => 'Cliente frecuente de barbería.'],
            ['nombre' => 'Luisa Fernanda Gómez', 'telefono' => '3209876543', 'email' => 'luisa.gomez@gmail.com', 'direccion' => 'Barrio Álamos, Calle 15 # 8-10', 'puntos_fidelizacion' => 110, 'notas' => 'Cliente VIP - Manicure quincenal.'],
            ['nombre' => 'Andrés Felipe Castro', 'telefono' => '3112345678', 'email' => 'andres.castro@gmail.com', 'direccion' => 'La Isabela, Cra 3 # 20-15', 'puntos_fidelizacion' => 35, 'notas' => 'Degradado alto.'],
            ['nombre' => 'Daniela Patricia Hoyos', 'telefono' => '3178901234', 'email' => 'daniela.hoyos@yahoo.es', 'direccion' => 'Barrio San Jerónimo, Calle 8 # 6-40', 'puntos_fidelizacion' => 60, 'notas' => 'Tratamientos de hidratación capilar.'],
            ['nombre' => 'Santiago Ramírez V.', 'telefono' => '3145678901', 'email' => 'santiago.ramirez@gmail.com', 'direccion' => 'Zaragoza, Lote 14', 'puntos_fidelizacion' => 15, 'notas' => 'Barba y bigote.'],
            ['nombre' => 'Camila Andrea Torres', 'telefono' => '3167890123', 'email' => 'camila.torres@hotmail.com', 'direccion' => 'Portal de Cartago, Torre 2 Apto 301', 'puntos_fidelizacion' => 95, 'notas' => 'Uñas acrílicas y diseño.'],
            ['nombre' => 'Mateo Henao Quintero', 'telefono' => '3190123456', 'email' => 'mateo.henao@gmail.com', 'direccion' => 'Barrio Santa Ana, Cra 9 # 14-22', 'puntos_fidelizacion' => 50, 'notas' => 'Corte urbano.'],
            ['nombre' => 'Valentina Ríos Osorio', 'telefono' => '3134567891', 'email' => 'valen.rios@outlook.com', 'direccion' => 'El Rosario, Calle 11 # 2-18', 'puntos_fidelizacion' => 130, 'notas' => 'Maquillaje para eventos sociales.'],
            ['nombre' => 'Jorge Iván Cárdenas', 'telefono' => '3109876542', 'email' => 'jorge.cardenas@gmail.com', 'direccion' => 'Barrio Ciprés, Calle 7 # 15-08', 'puntos_fidelizacion' => 25, 'notas' => 'Corte ejecutivo.'],
            ['nombre' => 'Natalia Sofía Pineda', 'telefono' => '3183456782', 'email' => 'natalia.pineda@gmail.com', 'direccion' => 'Centro, Calle 13 # 4-50', 'puntos_fidelizacion' => 70, 'notas' => 'Keratina y mantenimiento.']
        ];

        foreach ($clientes as $c) {
            Cliente::create($c);
        }

        // 2. Catálogo de Servicios
        $servicios = [
            ['nombre' => 'Corte Clásico & Peinado', 'categoria' => 'Barbería', 'precio' => 20000, 'duracion_minutos' => 30, 'descripcion' => 'Corte tradicional de caballero con lavado y peinado.'],
            ['nombre' => 'Degradado / Fade Urbano', 'categoria' => 'Barbería', 'precio' => 25000, 'duracion_minutos' => 45, 'descripcion' => 'Técnica de desvanecido con navaja y perfilado.'],
            ['nombre' => 'Ritual de Barba & Toalla Caliente', 'categoria' => 'Barbería', 'precio' => 18000, 'duracion_minutos' => 30, 'descripcion' => 'Vaporizador, aceites esenciales, toalla caliente y navaja.'],
            ['nombre' => 'Corte Dama & Blower', 'categoria' => 'Peluquería', 'precio' => 35000, 'duracion_minutos' => 50, 'descripcion' => 'Diseño de corte según visagismo y cepillado profesional.'],
            ['nombre' => 'Tinte & Baño de Color', 'categoria' => 'Peluquería', 'precio' => 85000, 'duracion_minutos' => 90, 'descripcion' => 'Aplicación de tinte profesional con nutrición profunda.'],
            ['nombre' => 'Keratina Brasileña Alisado', 'categoria' => 'Peluquería', 'precio' => 180000, 'duracion_minutos' => 180, 'descripcion' => 'Alisado progresivo con sellado térmico y brillo espejo.'],
            ['nombre' => 'Manicure Ruso & Esmaltado Semi', 'categoria' => 'Estética', 'precio' => 45000, 'duracion_minutos' => 60, 'descripcion' => 'Limpieza con torno, cutícula perfecta y esmaltado de larga duración.'],
            ['nombre' => 'Pedicure Spa con Exfoliación', 'categoria' => 'Estética', 'precio' => 40000, 'duracion_minutos' => 50, 'descripcion' => 'Hidromasaje, sales minerales, exfoliación y remoción de asperezas.'],
            ['nombre' => 'Limpieza Facial Profunda', 'categoria' => 'Spa', 'precio' => 70000, 'duracion_minutos' => 75, 'descripcion' => 'Extracción de impurezas, alta frecuencia y mascarilla hidratante.'],
            ['nombre' => 'Masaje Relajante Antiestrés', 'categoria' => 'Spa', 'precio' => 90000, 'duracion_minutos' => 60, 'descripcion' => 'Masaje corporal completo con aromaterapia y piedras volcánicas.']
        ];

        foreach ($servicios as $s) {
            Servicio::create($s);
        }

        // 3. Citas de prueba relacionadas
        $estilistas = ['Brayan Henao', 'Valentina Ríos', 'Sebastián Morales', 'Camila Ortiz', 'Mateo Gómez'];

        $citasData = [
            ['cliente_id' => 1, 'servicio_id' => 1, 'estilista' => 'Brayan Henao', 'dias' => 0, 'hora' => '09:00:00', 'estado' => 'completada', 'metodo_pago' => 'efectivo', 'notas' => 'Cliente satisfecho con el corte clásico.'],
            ['cliente_id' => 2, 'servicio_id' => 5, 'estilista' => 'Camila Ortiz', 'dias' => 0, 'hora' => '10:30:00', 'estado' => 'en_atencion', 'metodo_pago' => 'transferencia', 'notas' => 'Tinte castaño claro sin amoniaco.'],
            ['cliente_id' => 3, 'servicio_id' => 2, 'estilista' => 'Sebastián Morales', 'dias' => 0, 'hora' => '11:00:00', 'estado' => 'confirmada', 'metodo_pago' => 'qr_fachada', 'notas' => 'Fade medio con línea lateral.'],
            ['cliente_id' => 4, 'servicio_id' => 7, 'estilista' => 'Valentina Ríos', 'dias' => 0, 'hora' => '14:00:00', 'estado' => 'pendiente', 'metodo_pago' => 'pendiente', 'notas' => 'Diseño francés semipermanente.'],
            ['cliente_id' => 5, 'servicio_id' => 3, 'estilista' => 'Brayan Henao', 'dias' => 0, 'hora' => '15:30:00', 'estado' => 'confirmada', 'metodo_pago' => 'efectivo', 'notas' => 'Arreglo de barba perfilada.'],
            ['cliente_id' => 6, 'servicio_id' => 9, 'estilista' => 'Camila Ortiz', 'dias' => 1, 'hora' => '09:30:00', 'estado' => 'pendiente', 'metodo_pago' => 'pendiente', 'notas' => 'Sesión mensual de limpieza facial.'],
            ['cliente_id' => 7, 'servicio_id' => 2, 'estilista' => 'Sebastián Morales', 'dias' => 1, 'hora' => '11:30:00', 'estado' => 'confirmada', 'metodo_pago' => 'qr_fachada', 'notas' => 'Corte con desvanecido alto.'],
            ['cliente_id' => 8, 'servicio_id' => 8, 'estilista' => 'Valentina Ríos', 'dias' => 1, 'hora' => '14:30:00', 'estado' => 'pendiente', 'metodo_pago' => 'pendiente', 'notas' => 'Pedicure spa para descanso de pies.'],
            ['cliente_id' => 9, 'servicio_id' => 1, 'estilista' => 'Mateo Gómez', 'dias' => 2, 'hora' => '10:00:00', 'estado' => 'confirmada', 'metodo_pago' => 'efectivo', 'notas' => 'Corte y peinado con pomada mate.'],
            ['cliente_id' => 10, 'servicio_id' => 4, 'estilista' => 'Camila Ortiz', 'dias' => 2, 'hora' => '16:00:00', 'estado' => 'pendiente', 'metodo_pago' => 'pendiente', 'notas' => 'Corte en capas y secado.'],
            ['cliente_id' => 11, 'servicio_id' => 10, 'estilista' => 'Valentina Ríos', 'dias' => 3, 'hora' => '15:00:00', 'estado' => 'confirmada', 'metodo_pago' => 'transferencia', 'notas' => 'Masaje descontracturante de espalda.'],
            ['cliente_id' => 12, 'servicio_id' => 6, 'estilista' => 'Camila Ortiz', 'dias' => 4, 'hora' => '09:00:00', 'estado' => 'confirmada', 'metodo_pago' => 'qr_fachada', 'notas' => 'Alisado de keratina orgánica.']
        ];

        foreach ($citasData as $cd) {
            $servicio = Servicio::find($cd['servicio_id']);
            $fecha = Carbon::today()->addDays($cd['dias'])->format('Y-m-d') . ' ' . $cd['hora'];

            Cita::create([
                'cliente_id' => $cd['cliente_id'],
                'servicio_id' => $cd['servicio_id'],
                'estilista' => $cd['estilista'],
                'fecha_hora' => $fecha,
                'estado' => $cd['estado'],
                'total' => $servicio ? $servicio->precio : 25000,
                'metodo_pago' => $cd['metodo_pago'],
                'notas' => $cd['notas']
            ]);
        }

        // 4. Categorías y Productos de la Empresa (Clase 4)
        $this->call(CategorySeeder::class);
    }
}

