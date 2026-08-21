<?php

namespace Tests\Feature;

use App\Models\BillsModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillsTest extends TestCase
{
    use RefreshDatabase;

    const API_URL = '/api/bills';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_create_bills(): void
    {
        $data = BillsModel::factory()->make()->toArray();
        $response = $this->post(self::API_URL, $data);
        $response->assertStatus(200);
    }

    public function test_display_bills(): void
    {
        $response = $this->get(self::API_URL);
        $response->assertStatus(200);
    }

    public function test_update_patch_bills(): void
    {
        $response = $this->patch(self::API_URL.'/1', [
            'name' => 'Madelyca conguez',
        ]);
        $response->assertStatus(200);
    }

    public function test_update_put_bills(): void
    {
        $response = $this->put(self::API_URL.'/2', [
            'name' => 'Namitot',
            'amount' => 1023.10,
        ]);
        $response->assertStatus(200);
    }
}
