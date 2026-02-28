<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BuildingCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles
        Role::create(['name' => 'owner']);
        $this->owner = User::factory()->create();
        $this->owner->assignRole('owner');
    }

    public function test_owner_can_list_buildings()
    {
        Building::create(['name' => 'برج النخيل', 'settings' => []]);

        $response = $this->actingAs($this->owner)->get(route('buildings.index'));
        $response->assertOk();
        $response->assertSee('برج النخيل');
    }

    public function test_owner_can_create_building()
    {
        $response = $this->actingAs($this->owner)->post(route('buildings.store'), [
            'name'    => 'برج التجارة',
            'city'    => 'الرياض',
            'address' => 'شارع العليا',
            'settings' => [
                'late_penalty_type'  => 'percent',
                'late_penalty_value' => 5,
            ],
        ]);

        $response->assertRedirect(route('buildings.index'));
        $this->assertDatabaseHas('buildings', ['name' => 'برج التجارة']);
    }

    public function test_building_name_is_required()
    {
        $response = $this->actingAs($this->owner)->post(route('buildings.store'), [
            'name' => '',
        ]);
        $response->assertSessionHasErrors('name');
    }

    public function test_owner_can_update_building()
    {
        $building = Building::create(['name' => 'Old Name', 'settings' => []]);

        $response = $this->actingAs($this->owner)->put(route('buildings.update', $building), [
            'name'     => 'New Name',
            'settings' => ['late_penalty_type' => 'none'],
        ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $building->fresh()->name);
    }

    public function test_owner_can_delete_building()
    {
        $building = Building::create(['name' => 'To Delete', 'settings' => []]);

        $this->actingAs($this->owner)->delete(route('buildings.destroy', $building));

        $this->assertSoftDeleted('buildings', ['id' => $building->id]);
    }

    public function test_guest_cannot_access_buildings()
    {
        $response = $this->get(route('buildings.index'));
        $response->assertRedirect(route('login'));
    }
}
