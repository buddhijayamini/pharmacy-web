<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;

class CustomerTest extends TestCase
{
    /**
     * A basic test example.
     */
    use DatabaseTransactions, WithFaker;

    public function testCreateCustomer()
    {
        // Create a user with appropriate role
        $user = User::factory()->create();
        $user->assignRole('admin', 'api'); // Assuming 'admin' role has permission to create customers

        // Generate fake customer data
        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail
        ];

        // Make a POST request to create a customer record
        $response = $this->actingAs($user)
                         ->post('/api/customers', $customerData);

        // Assert that the request was successful (status code 201)
        $response->assertStatus(201);

        // Assert that the customer record exists in the database
        $this->assertDatabaseHas('customers', $customerData);
    }
}
