<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResellerScannerPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseller_can_pass_scanner_middleware()
    {
        // Create a user with 'reseller' role
        $reseller = User::factory()->create(['role' => 'reseller']);

        // Create a route protected by 'scanner' middleware for testing
        // Note: In a real app, we'd test an existing route, but for unit testing middleware effect:
        \Illuminate\Support\Facades\Route::middleware(['web', 'scanner'])->get('/test-scanner-route', function () {
            return 'OK';
        });

        // Act as reseller and try to access the scanner route
        $response = $this->actingAs($reseller)->get('/test-scanner-route');

        // Assert OK (200), meaning middleware allowed it
        $response->assertStatus(200);
        $response->assertSee('OK');
    }

    public function test_scanner_can_pass_scanner_middleware()
    {
        $scanner = User::factory()->create(['role' => 'scanner']);

        \Illuminate\Support\Facades\Route::middleware(['web', 'scanner'])->get('/test-scanner-route-2', function () {
            return 'OK';
        });

        $response = $this->actingAs($scanner)->get('/test-scanner-route-2');

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_pass_scanner_middleware()
    {
        $user = User::factory()->create(['role' => 'user']); // or 'customer'

        \Illuminate\Support\Facades\Route::middleware(['web', 'scanner'])->get('/test-scanner-route-3', function () {
            return 'OK';
        });

        $response = $this->actingAs($user)->get('/test-scanner-route-3');

        $response->assertStatus(403);
    }

    public function test_reseller_can_scan_assigned_event()
    {
        $reseller = User::factory()->create(['role' => 'reseller']);
        $event = Event::factory()->create();

        // Assign reseller to event
        $event->resellers()->attach($reseller->id);

        // Check canScan logic
        $this->assertTrue($reseller->canScan($event));
    }

    public function test_reseller_cannot_scan_unassigned_event()
    {
        $reseller = User::factory()->create(['role' => 'reseller']);
        $event = Event::factory()->create();

        // Do NOT assign

        $this->assertFalse($reseller->canScan($event));
    }
}
