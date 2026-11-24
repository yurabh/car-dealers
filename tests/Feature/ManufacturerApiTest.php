<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Dealer;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManufacturerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_manufacturers_with_counts(): void
    {
        $manufacturer = Manufacturer::factory()->create(['name' => 'Toyota']);
        Dealer::factory()->count(2)->for($manufacturer)->create();

        $this->getJson('/api/manufacturers')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Toyota')
            ->assertJsonPath('data.0.dealers_count', 2)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_it_creates_shows_updates_and_deletes_a_manufacturer(): void
    {
        $id = $this->postJson('/api/manufacturers', ['name' => 'BMW'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'BMW')
            ->json('data.id');

        $this->getJson("/api/manufacturers/{$id}")->assertOk()->assertJsonPath('data.name', 'BMW');

        $this->putJson("/api/manufacturers/{$id}", ['name' => 'BMW Group'])
            ->assertOk()
            ->assertJsonPath('data.name', 'BMW Group');

        $this->deleteJson("/api/manufacturers/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('manufacturers', ['id' => $id]);
    }

    public function test_name_is_required_and_unique(): void
    {
        Manufacturer::factory()->create(['name' => 'Ford']);

        $this->postJson('/api/manufacturers', [])->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->postJson('/api/manufacturers', ['name' => 'Ford'])->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_put_without_name_is_rejected_but_patch_without_name_is_allowed(): void
    {
        $manufacturer = Manufacturer::factory()->create(['name' => 'Ford']);

        $this->putJson("/api/manufacturers/{$manufacturer->id}", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->patchJson("/api/manufacturers/{$manufacturer->id}", [])
            ->assertOk()
            ->assertJsonPath('data.name', 'Ford');
    }

    public function test_updating_with_the_same_name_is_allowed(): void
    {
        $manufacturer = Manufacturer::factory()->create(['name' => 'Ford']);

        $this->putJson("/api/manufacturers/{$manufacturer->id}", ['name' => 'Ford'])->assertOk();
    }

    public function test_deleting_a_manufacturer_resyncs_cars_amount_of_other_dealers(): void
    {
        $toyota = Manufacturer::factory()->create();
        $bmw = Manufacturer::factory()->create();
        $bmwDealer = Dealer::factory()->for($bmw)->create();

        Car::factory()->count(2)->create(['dealer_id' => $bmwDealer->id, 'manufacturer_id' => $toyota->id]);
        Car::factory()->create(['dealer_id' => $bmwDealer->id, 'manufacturer_id' => $bmw->id]);
        $this->assertSame(3, $bmwDealer->fresh()->cars_amount);

        $this->deleteJson("/api/manufacturers/{$toyota->id}")->assertNoContent();

        $this->assertSame(1, $bmwDealer->fresh()->cars_amount);
    }
}
