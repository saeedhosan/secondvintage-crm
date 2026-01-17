<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Watch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

final class WatchFeatureTest extends TestCase
{
    use RefreshDatabase;

    private const string WATCH_MANAGEMENT_PERMISSION = 'watchManagement';

    public function test_user_can_view_watch_index(): void
    {
        $user = $this->createAuthorizedUser();
        Watch::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->get(route('watches.index'));

        $response->assertOk();
    }

    public function test_user_can_create_watch(): void
    {
        $user = $this->createAuthorizedUser();
        $watchData = Watch::factory()->make()->toArray();

        $response = $this->actingAs($user)
            ->post(route('watches.store'), $watchData);

        $response->assertRedirect();
        $this->assertDatabaseHas('watches', ['name' => $watchData['name']]);
    }

    public function test_user_can_update_watch(): void
    {
        $user = $this->createAuthorizedUser();
        $watch = Watch::factory()->create();
        $updatedData = ['name' => 'Updated Watch Name'];

        $response = $this->actingAs($user)
            ->put(route('watches.update', $watch), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('watches', $updatedData);
    }

    public function test_user_can_delete_watch(): void
    {
        $user = $this->createAuthorizedUser();
        $watch = Watch::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('watches.destroy', $watch));

        $response->assertRedirect();
        $this->assertNotNull($watch->fresh()->deleted_at);
    }

    public function test_user_can_approve_watch(): void
    {
        $user = $this->createAuthorizedUser();
        $watch = Watch::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)
            ->post(route('watches.approve', $watch));

        $response->assertRedirect();
        $this->assertDatabaseHas('watches', [
            'id' => $watch->id,
            'status' => 'approved'
        ]);
    }

    public function test_user_can_perform_bulk_status_update(): void
    {
        $user = $this->createAuthorizedUser();
        $watches = Watch::factory()->count(2)->create(['status' => 'pending']);
        $watchIds = $watches->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->post(route('watches.bulk-actions'), [
                'ids' => $watchIds,
                'action' => 'status',
                'value' => 'approved',
            ]);

        $response->assertRedirect();

        foreach ($watchIds as $watchId) {
            $this->assertDatabaseHas('watches', [
                'id' => $watchId,
                'status' => 'approved'
            ]);
        }
    }

    public function test_unauthorized_user_cannot_access_watches(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('watches.index'));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_watches(): void
    {
        $response = $this->get(route('watches.index'));

        $response->assertRedirect('login');
    }

    private function createAuthorizedUser(): User
    {
        $user = User::factory()->create();
        
        Permission::firstOrCreate(['name' => self::WATCH_MANAGEMENT_PERMISSION]);
        $user->givePermissionTo(self::WATCH_MANAGEMENT_PERMISSION);

        return $user;
    }
}
