<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Flete;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/fletes')->assertRedirect('/login');
    }

    public function test_store_calculates_total_on_the_server(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/fletes', [
            'cliente_nombre_busqueda' => 'Mauro peña',
            'fecha' => '2026-11-15',
            'items' => [
                [
                    'fecha' => '2026-11-15',
                    'descripcion' => '1 caja',
                    'servicio' => '25',
                    'flete' => '40',
                    'total' => '999.99',
                ],
            ],
        ]);

        $response->assertRedirect(route('fletes.index'));

        $flete = Flete::query()->first();

        $this->assertNotNull($flete);
        $this->assertEquals(65.0, (float) $flete->total_general);
        $this->assertEquals(65.0, (float) $flete->items()->value('total'));
        $this->assertSame('Mauro peña', $flete->cliente->nombre);
    }

    public function test_store_reuses_an_existing_client(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::query()->create(['nombre' => 'Ruth']);

        $this->actingAs($user)->post('/fletes', [
            'cliente_nombre_busqueda' => 'Ruth',
            'fecha' => '2026-11-15',
            'items' => [
                [
                    'fecha' => '2026-11-15',
                    'descripcion' => '1 pqte',
                    'servicio' => '10',
                    'flete' => '5',
                ],
            ],
        ])->assertRedirect(route('fletes.index'));

        $this->assertSame(1, Cliente::query()->count());
        $this->assertSame($cliente->id, Flete::query()->value('cliente_id'));
    }

    public function test_payment_cannot_exceed_balance(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::query()->create(['nombre' => 'Yuly']);
        $flete = Flete::query()->create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-15',
            'total_general' => 50,
        ]);

        $this->actingAs($user)->post(route('fletes.pagos.store', $flete), [
            'descripcion' => 'Abono',
            'monto' => '50.01',
        ])->assertSessionHasErrors('monto');

        $this->assertSame(0, $flete->pagos()->count());
    }

    public function test_update_cannot_drop_total_below_payments(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::query()->create(['nombre' => 'Dona']);
        $flete = Flete::query()->create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-15',
            'total_general' => 100,
        ]);
        $flete->pagos()->create([
            'descripcion' => 'Abono',
            'monto' => 80,
        ]);

        $this->actingAs($user)->put(route('fletes.update', $flete), [
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-15',
            'items' => [
                [
                    'fecha' => '2026-11-15',
                    'descripcion' => '1 caja',
                    'servicio' => '10',
                    'flete' => '10',
                ],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame('100.00', $flete->fresh()->total_general);
    }

    public function test_cannot_delete_a_payment_from_another_flete(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::query()->create(['nombre' => 'Pele']);
        $first = Flete::query()->create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-15',
            'total_general' => 40,
        ]);
        $second = Flete::query()->create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-15',
            'total_general' => 40,
        ]);
        $pago = $first->pagos()->create([
            'descripcion' => 'Yape',
            'monto' => 10,
        ]);

        $this->actingAs($user)
            ->delete(route('fletes.pagos.destroy', [$second, $pago]))
            ->assertNotFound();

        $this->assertDatabaseHas('flete_pagos', ['id' => $pago->id]);
    }

    public function test_search_matches_client_name(): void
    {
        $user = User::factory()->create();
        $ruth = Cliente::query()->create(['nombre' => 'Ruth']);
        $mary = Cliente::query()->create(['nombre' => 'Mary']);

        Flete::query()->create([
            'cliente_id' => $ruth->id,
            'fecha' => '2026-11-15',
            'total_general' => 50,
        ]);
        Flete::query()->create([
            'cliente_id' => $mary->id,
            'fecha' => '2026-11-15',
            'total_general' => 80,
        ]);

        $this->actingAs($user)
            ->get('/fletes?q=Ruth')
            ->assertOk()
            ->assertSee('Ruth')
            ->assertDontSee('Mary');
    }
}
