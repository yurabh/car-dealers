<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Dealer;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_shows_updates_and_deletes_a_car(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $dealer = Dealer::factory()->create();

        $id = $this->postJson('/api/cars', [
            'manufacturer_id' => $manufacturer->id,
            'dealer_id' => $dealer->id,
            'model' => 'Corolla',
            'size' => 'compact',
            'color' => 'red',
            'fuel' => 'hybrid',
        ])
            ->assertCreated()
            ->assertJsonPath('data.model', 'Corolla')
            ->assertJsonPath('data.fuel', 'hybrid')
            ->assertJsonPath('data.dealer.id', $dealer->id)
            ->json('data.id');

        $this->getJson("/api/cars/{$id}")->assertOk()->assertJsonPath('data.color', 'red');

        $this->patchJson("/api/cars/{$id}", ['color' => 'blue'])
            ->assertOk()
            ->assertJsonPath('data.color', 'blue')
            ->assertJsonPath('data.model', 'Corolla');

        $this->deleteJson("/api/cars/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('cars', ['id' => $id]);
    }

    public function test_put_requires_all_fields_while_patch_accepts_partial_body(): void
    {
        $car = Car::factory()->create();

        $this->putJson("/api/cars/{$car->id}", ['color' => 'blue'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['manufacturer_id', 'dealer_id', 'model', 'size', 'fuel']);

        $this->putJson("/api/cars/{$car->id}", [
            'manufacturer_id' => $car->manufacturer_id,
            'dealer_id' => $car->dealer_id,
            'model' => 'Replaced',
            'size' => 'suv',
            'color' => 'blue',
            'fuel' => 'diesel',
        ])->assertOk()->assertJsonPath('data.model', 'Replaced');

        $this->patchJson("/api/cars/{$car->id}", ['color' => 'red'])
            ->assertOk()
            ->assertJsonPath('data.color', 'red')
            ->assertJsonPath('data.model', 'Replaced');
    }

    public function test_it_validates_car_fields(): void
    {
        $this->postJson('/api/cars', ['size' => 'huge', 'color' => 'rainbow', 'fuel' => 'water'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['manufacturer_id', 'dealer_id', 'model', 'size', 'color', 'fuel']);
    }

    public function test_it_filters_by_any_combination(): void
    {
        $toyota = Manufacturer::factory()->create();
        $tesla = Manufacturer::factory()->create();

        $match = Car::factory()->create(['manufacturer_id' => $tesla->id, 'fuel' => 'electric', 'color' => 'red', 'size' => 'suv']);
        Car::factory()->create(['manufacturer_id' => $tesla->id, 'fuel' => 'electric', 'color' => 'white', 'size' => 'midsize']);
        Car::factory()->create(['manufacturer_id' => $toyota->id, 'fuel' => 'petrol', 'color' => 'red', 'size' => 'suv']);

        $this->getJson('/api/cars?fuel[]=electric')->assertJsonCount(2, 'data');
        $this->getJson('/api/cars?color[]=red')->assertJsonCount(2, 'data');
        $this->getJson('/api/cars?color[]=red&color[]=white')->assertJsonCount(3, 'data');
        $this->getJson("/api/cars?manufacturer_id[]={$toyota->id}")->assertJsonCount(1, 'data');

        $this->getJson('/api/cars?fuel[]=electric&color[]=red&size[]=suv')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);

        $this->getJson('/api/cars?fuel[]=diesel')->assertJsonCount(0, 'data');
    }

    public function test_it_filters_by_dealer(): void
    {
        $dealer = Dealer::factory()->create();
        Car::factory()->count(2)->create(['dealer_id' => $dealer->id]);
        Car::factory()->create();

        $this->getJson("/api/cars?dealer_id[]={$dealer->id}")->assertJsonCount(2, 'data');
    }

    public function test_it_rejects_invalid_filter_values(): void
    {
        $this->getJson('/api/cars?fuel[]=water')->assertUnprocessable()->assertJsonValidationErrors('fuel.0');
    }

    public function test_cars_amount_follows_create_move_and_delete(): void
    {
        $first = Dealer::factory()->create();
        $second = Dealer::factory()->create();
        $car = Car::factory()->create(['dealer_id' => $first->id]);
        Car::factory()->create(['dealer_id' => $first->id]);

        $this->assertSame(2, $first->fresh()->cars_amount);

        $this->patchJson("/api/cars/{$car->id}", ['dealer_id' => $second->id])->assertOk();
        $this->assertSame(1, $first->fresh()->cars_amount);
        $this->assertSame(1, $second->fresh()->cars_amount);

        $this->deleteJson("/api/cars/{$car->id}")->assertNoContent();
        $this->assertSame(0, $second->fresh()->cars_amount);
    }

    public function test_filter_options_for_cars(): void
    {
        $this->getJson('/api/filters/cars')
            ->assertOk()
            ->assertJsonPath('data.fuels', ['petrol', 'diesel', 'electric', 'hybrid', 'gas'])
            ->assertJsonStructure(['data' => ['manufacturers', 'dealers', 'sizes', 'colors', 'fuels']]);
    }
}
