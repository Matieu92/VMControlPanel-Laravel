<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_uzytkownicy_nie_moga_widziec_serwerow_innych()
    {
        $clientA = User::factory()->create(['role' => 'client']);
        $clientB = User::factory()->create(['role' => 'client']);
        $serverB = Server::factory()->create(['user_id' => $clientB->id, 'hostname' => 'ukryty-vps']);

        $response = $this->actingAs($clientA)->get(route('servers.index'));

        $response->assertDontSee('ukryty-vps');
    }

    public function test_wplata_pieniedzy_zwieksza_balans_uzytkownika()
    {
        $user = User::factory()->create(['balance' => 100.00]);

        $this->actingAs($user)->post(route('finance.deposit'), [
            'amount' => 50.00
        ]);

        $this->assertEquals(150.00, $user->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 50.00,
            'type' => 'deposit'
        ]);
    }
}