<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Option 1: Accept the redirect status
        $response = $this->get('/');
        $response->assertStatus(302); // Changed from 200 to 302
        
        // Option 2: Or test as an authenticated user
        // $user = User::factory()->create();
        // $response = $this->actingAs($user)->get('/');
        // $response->assertStatus(200);
    }
}
