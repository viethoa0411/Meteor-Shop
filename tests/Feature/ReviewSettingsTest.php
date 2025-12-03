<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewSettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test auto_approve setting
     */
    public function test_auto_approve_setting()
    {
        // Set auto_approve to true
        config(['reviews.auto_approve' => true]);
        
        $user = User::factory()->create(['role' => 'user']);
        $product = Product::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->postJson("/products/{$product->slug}/review", [
            'rating' => 5,
            'comment' => 'Great product!',
        ]);
        
        $review = Review::where('product_id', $product->id)->first();
        $this->assertEquals('approved', $review->status);
    }

    /**
     * Test blacklist keywords
     */
    public function test_blacklist_keywords()
    {
        config(['reviews.blacklist_keywords' => ['spam', 'test']]);
        config(['reviews.auto_hide_blacklist' => true]);
        
        $user = User::factory()->create(['role' => 'user']);
        $product = Product::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->postJson("/products/{$product->slug}/review", [
            'rating' => 1,
            'comment' => 'This is spam content',
        ]);
        
        $review = Review::where('product_id', $product->id)->first();
        $this->assertEquals('hidden', $review->status);
    }

    /**
     * Test max_images setting
     */
    public function test_max_images_setting()
    {
        config(['reviews.allow_images' => true]);
        config(['reviews.max_images' => 3]);
        
        // Test that only 3 images are accepted
        // Implementation depends on your validation logic
    }
}


