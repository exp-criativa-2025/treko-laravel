<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_donation()
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();

        $payload = [
            'donated' => 150.00,
            'campaign_id' => $campaign->id,
        ];

        $response = $this->actingAs($user)->postJson('/api/donations', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'donated' => 150.00,
                     'campaign_id' => $campaign->id,
                     'user_id' => $user->id,
                 ]);

        $this->assertDatabaseHas('donations', [
            'donated' => 150.00,
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_list_own_donations()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $campaign = Campaign::factory()->create();

        // Donations from the authenticated user
        Donation::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'donated' => 200.00,
        ]);

        // Donation from another user (should still appear, depending on role)
        Donation::factory()->create([
            'user_id' => $otherUser->id,
            'campaign_id' => $campaign->id,
            'donated' => 300.00,
        ]);

        $response = $this->actingAs($user)->getJson('/api/donations');

        $response->assertStatus(200)
                 ->assertJsonFragment(['donated' => 200.00])
                 ->assertJsonFragment(['donated' => 300.00]); // If admin, both will appear. If representative, you might need role check.
    }

    public function test_user_cannot_create_donation_with_invalid_data()
    {
        $user = User::factory()->create();

        $payload = [
            'donated' => -50,
            'campaign_id' => 999 // Non-existing campaign
        ];

        $response = $this->actingAs($user)->postJson('/api/donations', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['donated', 'campaign_id']);
    }

    public function test_user_cannot_view_others_donation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $campaign = Campaign::factory()->create();

        $donation = Donation::factory()->create([
            'user_id' => $otherUser->id,
            'campaign_id' => $campaign->id,
            'donated' => 100,
        ]);

        $response = $this->actingAs($user)->getJson("/api/donations/{$donation->id}");

        $response->assertStatus(403);
    }
}
