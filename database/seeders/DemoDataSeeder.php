<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        $now = now();

        DB::transaction(function () use ($faker, $now): void {
            DB::table('pagos')->where('observacion', 'like', '[SEED]%')->delete();
            DB::table('servicios')->where('observaciones', 'like', '[SEED]%')->delete();
            DB::table('clientes')->where('observaciones', 'like', '[SEED]%')->delete();
            DB::table('agencias')->where('observaciones', 'like', '[SEED]%')->delete();

            $users = [
                [
                    'name' => 'Administrador Demo',
                    'email' => 'admin@demo.com',
                ],
                [
                    'name' => 'Operador Demo',
                    'email' => 'operador@demo.com',
                ],
                [
                    'name' => 'Consulta Demo',
                    'email' => 'consulta@demo.com',
                ],
            ];

            foreach ($users as $user) {
                DB::table('users')->updateOrInsert(
                    ['email' => $user['email']],
                    [
                        'name' => $user['name'],
                        'email_verified_at' => $now,
                        'password' => Hash::make('password'),
                        'remember_token' => $faker->bothify('??????????'),
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }

            $clientes = [
                ['nombre' => 'Transportes Rivera SAC', 'telefono' => '987123456', 'direccion' => 'Av. Argentina 1450, Lima'],
                ['nombre' => 'Comercial Huamán EIRL', 'telefono' => '965222341', 'direccion' => 'Jr. Junín 420, La Victoria'],
                ['nombre' => 'Inversiones Santa Rosa', 'telefono' => '944781235', 'direccion' => 'Av. México 1180, Lima'],
                ['nombre' => 'Distribuidora El Norte', 'telefono' => '955341278', 'direccion' => 'Mz. B Lt. 8, SMP'],
                ['nombre' => 'Abarrotes Medina', 'telefono' => '978441122', 'direccion' => 'Av. Universitaria 5321, Los Olivos'],
                ['nombre' => 'Ferretería Vargas', 'telefono' => '966120045', 'direccion' => 'Jr. Puno 885, Cercado'],
                ['nombre' => 'Textiles Emanuel', 'telefono' => '973888221', 'direccion' => 'Gamarra, Galería San Pedro'],
                ['nombre' => 'Mercado Central Puesto 24', 'telefono' => '981004455', 'direccion' => 'Cercado de Lima'],
            ];

            $agencias = [
                ['nombre' => 'Shalom', 'telefono' => '014567890', 'direccion' => 'Av. Nicolás Arriola 900, La Victoria'],
                ['nombre' => 'Olva Courier', 'telefono' => '015550000', 'direccion' => 'Av. Tomás Marsano 1500, Surquillo'],
                ['nombre' => 'Marvisur', 'telefono' => '016203040', 'direccion' => 'Av. Manco Cápac 410, La Victoria'],
                ['nombre' => 'Flores Hermanos', 'telefono' => '017777111', 'direccion' => 'Av. 28 de Julio 1300, La Victoria'],
                ['nombre' => 'Civa Express', 'telefono' => '018888222', 'direccion' => 'Paseo de la República 1200, Lima'],
            ];

            $clienteIds = [];

            foreach ($clientes as $cliente) {
                $clienteIds[] = DB::table('clientes')->insertGetId([
                    'nombre' => $cliente['nombre'],
                    'telefono' => $cliente['telefono'],
                    'direccion' => $cliente['direccion'],
                    'observaciones' => '[SEED] Cliente de prueba',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $agenciaIds = [];

            foreach ($agencias as $agencia) {
                $agenciaIds[] = DB::table('agencias')->insertGetId([
                    'nombre' => $agencia['nombre'],
                    'telefono' => $agencia['telefono'],
                    'direccion' => $agencia['direccion'],
                    'observaciones' => '[SEED] Agencia de prueba',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $mercancias = [
                'Cajas de repuestos',
                'Paquetes de abarrotes',
                'Rollos de tela',
                'Documentación y sobres',
                'Herramientas de ferretería',
                'Productos de limpieza',
                'Equipos electrónicos',
                'Bultos variados para provincia',
            ];

            for ($index = 1; $index <= 24; $index++) {
                $fechaServicio = Carbon::now()->subDays(random_int(1, 45))->setTime(random_int(7, 18), [0, 15, 30, 45][array_rand([0, 15, 30, 45])]);
                $costoTransporte = random_int(30, 180);
                $costoFlete = random_int(20, 140);
                $total = $costoTransporte + $costoFlete;
                $estadoServicio = random_int(1, 100) <= 65 ? 'ENTREGADO' : 'PENDIENTE';
                $fechaEntrega = $estadoServicio === 'ENTREGADO'
                    ? (clone $fechaServicio)->addHours(random_int(6, 72))
                    : null;

                $tipoPago = ['PENDIENTE', 'PARCIAL', 'PAGADO'][array_rand(['PENDIENTE', 'PARCIAL', 'PAGADO'])];
                $montoPagado = 0;

                if ($tipoPago === 'PARCIAL') {
                    $montoPagado = round($total * (random_int(30, 80) / 100), 2);
                }

                if ($tipoPago === 'PAGADO') {
                    $montoPagado = $total;
                }

                $servicioId = DB::table('servicios')->insertGetId([
                    'cliente_id' => $clienteIds[array_rand($clienteIds)],
                    'agencia_id' => $agenciaIds[array_rand($agenciaIds)],
                    'tipo_servicio' => random_int(0, 1) === 1 ? 'ENVIO' : 'RECOJO',
                    'fecha_servicio' => $fechaServicio,
                    'cantidad_bultos' => random_int(1, 12),
                    'descripcion' => $mercancias[array_rand($mercancias)],
                    'costo_transporte' => $costoTransporte,
                    'costo_flete' => $costoFlete,
                    'total' => $total,
                    'estado_servicio' => $estadoServicio,
                    'estado_pago' => $tipoPago,
                    'fecha_entrega' => $fechaEntrega,
                    'observaciones' => '[SEED] Servicio de prueba #' . $index,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                if ($montoPagado <= 0) {
                    continue;
                }

                $cantidadPagos = $tipoPago === 'PAGADO' && random_int(0, 1) === 1 ? 2 : 1;

                if ($cantidadPagos === 1) {
                    DB::table('pagos')->insert([
                        'servicio_id' => $servicioId,
                        'fecha_pago' => $fechaEntrega ?? (clone $fechaServicio)->addHours(random_int(2, 24)),
                        'monto' => $montoPagado,
                        'metodo_pago' => ['EFECTIVO', 'YAPE', 'PLIN', 'TRANSFERENCIA'][array_rand(['EFECTIVO', 'YAPE', 'PLIN', 'TRANSFERENCIA'])],
                        'observacion' => '[SEED] Pago registrado automáticamente',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    continue;
                }

                $primerPago = round($montoPagado * 0.5, 2);
                $segundoPago = round($montoPagado - $primerPago, 2);

                DB::table('pagos')->insert([
                    [
                        'servicio_id' => $servicioId,
                        'fecha_pago' => (clone $fechaServicio)->addHours(random_int(2, 12)),
                        'monto' => $primerPago,
                        'metodo_pago' => 'YAPE',
                        'observacion' => '[SEED] Adelanto del servicio',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'servicio_id' => $servicioId,
                        'fecha_pago' => $fechaEntrega ?? (clone $fechaServicio)->addHours(random_int(24, 72)),
                        'monto' => $segundoPago,
                        'metodo_pago' => 'TRANSFERENCIA',
                        'observacion' => '[SEED] Cancelación del servicio',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ]);
            }
        });
    }
}