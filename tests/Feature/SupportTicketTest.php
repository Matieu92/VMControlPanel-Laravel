<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_kategoria_emergency_zmienia_priorytet_ticketa_na_wysoki()
    {
        $user = User::factory()->create(['role' => 'client']);

        $this->actingAs($user)->post(route('support.store'), [
            'category' => 'emergency',
            'subject' => 'Awaria krytyczna węzła',
            'message' => 'Opis błędu mający minimum dziesięć znaków.',
            'server_id' => null
        ]);

        $this->assertDatabaseHas('support_tickets', [
            'category' => 'emergency',
            'priority' => 'high'
        ]);
    }

    public function test_formularz_ticketa_spelnia_wcag()
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->get(route('support.create'));

        $response->assertStatus(200);
        $response->assertSee('role="radiogroup"', false);
        $response->assertSee('aria-labelledby="category-label"', false);
    }

    public function test_klient_nie_ma_dostepu_do_logow_admina()
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get('/admin/logs');

        $response->assertStatus(403);
    }
}