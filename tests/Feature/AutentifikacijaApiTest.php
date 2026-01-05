<?php

namespace Tests\Feature;

use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutentifikacijaApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // reikalinga, kad assignRole('vartotojas') neskristų
        $this->seed(RolesSeeder::class);
    }

    public function test_vartotojas_gali_registruotis_ir_gauti_token(): void
    {
        $response = $this->postJson('/api/registruotis', [
            'vardas' => 'Testas',
            'el_pastas' => 'testas@auto.lt',
            'slaptazodis' => 'slaptazodis',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'zinute',
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testas@auto.lt',
        ]);
    }

    public function test_vartotojas_gali_prisijungti_ir_gauti_token(): void
    {
        // pirmiau registruojam
        $this->postJson('/api/registruotis', [
            'vardas' => 'Testas',
            'el_pastas' => 'testas@auto.lt',
            'slaptazodis' => 'slaptazodis',
        ])->assertStatus(201);

        // tada prisijungiam
        $response = $this->postJson('/api/prisijungti', [
            'el_pastas' => 'testas@auto.lt',
            'slaptazodis' => 'slaptazodis',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'zinute',
                'token',
            ]);
    }

    public function test_as_endpointas_reikalauja_tokeno(): void
    {
        $this->getJson('/api/as')
            ->assertStatus(401);
    }
}
