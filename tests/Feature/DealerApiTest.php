<?php

namespace Tests\Feature;

use App\Models\Dealer;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_shows_updates_and_deletes_a_dealer(): void
    {
        $manufacturer = Manufacturer::factory()->create();

        $id = $this->postJson('/api/dealers', [
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Kyiv Motors',
            'city' => 'Kyiv',
            'country' => 'Ukraine',
            'employees_count' => 25,
            'cars_amount' => 999, // ignored: computed from cars
        ])
            ->assertCreated()
            ->assertJsonPath('data.city', 'Kyiv')
            ->assertJsonPath('data.cars_amount', 0)
            ->assertJsonPath('data.manufacturer.id', $manufacturer->id)
            ->json('data.id');

        $this->getJson("/api/dealers/{$id}")->assertOk()->assertJsonPath('data.name', 'Kyiv Motors');

        $this->patchJson("/api/dealers/{$id}", ['employees_count' => 30])
            ->assertOk()
            ->assertJsonPath('data.employees_count', 30)
            ->assertJsonPath('data.city', 'Kyiv');

        $this->deleteJson("/api/dealers/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('dealers', ['id' => $id]);
    }

    public function test_put_requires_all_fields_while_patch_accepts_partial_body(): void
    {
        $dealer = Dealer::factory()->create(['city' => 'Kyiv']);

        $this->putJson("/api/dealers/{$dealer->id}", ['employees_count' => 16])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['manufacturer_id', 'name', 'city', 'country']);

        $this->putJson("/api/dealers/{$dealer->id}", [
            'manufacturer_id' => $dealer->manufacturer_id,
            'name' => 'Replaced',
            'city' => 'Lviv',
            'country' => 'Ukraine',
            'employees_count' => 16,
        ])->assertOk()->assertJsonPath('data.city', 'Lviv');

        $this->patchJson("/api/dealers/{$dealer->id}", ['employees_count' => 20])
            ->assertOk()
            ->assertJsonPath('data.employees_count', 20)
            ->assertJsonPath('data.city', 'Lviv');
    }

    public function test_it_validates_dealer_fields(): void
    {
        $this->postJson('/api/dealers', ['manufacturer_id' => 999, 'employees_count' => -1])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['manufacturer_id', 'name', 'city', 'country', 'employees_count']);
    }

    public function test_it_filters_by_any_combination(): void
    {
        $toyota = Manufacturer::factory()->create();
        $bmw = Manufacturer::factory()->create();

        $match = Dealer::factory()->for($toyota)->create(['country' => 'Ukraine', 'city' => 'Kyiv', 'employees_count' => 50]);
        Dealer::factory()->for($toyota)->create(['country' => 'Ukraine', 'city' => 'Lviv', 'employees_count' => 5]);
        Dealer::factory()->for($bmw)->create(['country' => 'Germany', 'city' => 'Berlin', 'employees_count' => 50]);

        $this->getJson('/api/dealers?country[]=Ukraine')->assertJsonCount(2, 'data');
        $this->getJson("/api/dealers?manufacturer_id[]={$bmw->id}")->assertJsonCount(1, 'data');
        $this->getJson('/api/dealers?employees_min=10')->assertJsonCount(2, 'data');
        $this->getJson('/api/dealers?country[]=Ukraine&country[]=Germany')->assertJsonCount(3, 'data');

        $this->getJson("/api/dealers?country[]=Ukraine&employees_min=10&manufacturer_id[]={$toyota->id}")
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_it_filters_by_cars_amount_range(): void
    {
        $few = Dealer::factory()->create();
        $many = Dealer::factory()->create();
        \App\Models\Car::factory()->count(1)->create(['dealer_id' => $few->id]);
        \App\Models\Car::factory()->count(5)->create(['dealer_id' => $many->id]);

        $this->getJson('/api/dealers?cars_amount_min=3')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $many->id);

        $this->getJson('/api/dealers?cars_amount_max=3')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $few->id);
    }

    public function test_it_rejects_unknown_sort_column(): void
    {
        $this->getJson('/api/dealers?sort=password')->assertUnprocessable();
    }

    public function test_filter_options_for_dealers(): void
    {
        Dealer::factory()->create(['country' => 'Ukraine', 'city' => 'Kyiv', 'employees_count' => 7]);

        $this->getJson('/api/filters/dealers')
            ->assertOk()
            ->assertJsonPath('data.countries', ['Ukraine'])
            ->assertJsonPath('data.employees_count.max', 7)
            ->assertJsonStructure(['data' => ['manufacturers', 'countries', 'cities', 'cars_amount', 'employees_count']]);
    }
}
