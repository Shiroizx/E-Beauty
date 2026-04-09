<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShippingStatusUpdated;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_page_can_be_accessed()
    {
        $response = $this->get('/track');
        $response->assertStatus(200);
        $response->assertSee('Lacak Pesanan Anda');
    }

    public function test_tracking_search_redirects_to_show()
    {
        $response = $this->post('/track', [
            'order_number' => 'EB-12345'
        ]);

        $response->assertRedirect('/track/EB-12345');
    }

    public function test_tracking_show_with_invalid_order_redirects_back()
    {
        $response = $this->get('/track/INVALID-ORDER');
        $response->assertRedirect('/track');
        $response->assertSessionHas('error');
    }

    public function test_admin_can_update_order_status_and_send_email()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@skinbae.id']);
        $user = User::factory()->create();
        
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'TEST-001',
            'status' => 'pending_payment',
            'payment_status' => 'pending',
            'payment_method' => 'bank_transfer',
            'subtotal' => 100000,
            'shipping_cost' => 10000,
            'total' => 110000,
            'shipping_name' => 'Test User',
            'shipping_phone' => '08123456789',
            'shipping_address_line' => 'Jl. Test 1',
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI Jakarta',
            'shipping_postal_code' => '12345',
        ]);

        $response = $this->actingAs($admin)->put("/admin/orders/{$order->id}", [
            'status' => 'shipped',
            'payment_status' => 'paid',
            'shipping_name' => 'Test User',
            'shipping_phone' => '08123456789',
            'shipping_address_line' => 'Jl. Test 1',
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI Jakarta',
            'shipping_postal_code' => '12345',
        ]);

        $response->assertRedirect();
        $this->assertEquals('shipped', $order->fresh()->status);
        
        Mail::assertQueued(ShippingStatusUpdated::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}