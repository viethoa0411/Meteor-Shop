<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductPriceFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_product_price_displays_integer_with_dots()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100000.00,
            'stock' => 10
        ]);

        $response = $this->actingAs($user)->get(route('admin.products.edit', $product->id));

        $response->assertStatus(200);
        // Check for dotted format value in input
        $response->assertSee('value="100.000"', false);
    }

    public function test_update_product_price_with_dots()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 50000,
        ]);

        $response = $this->actingAs($user)->put(route('admin.products.update', $product->id), [
            'name' => 'Updated Product',
            'category_id' => $category->id,
            'price' => '150.000', // Input with dots
            'status' => 'active',
            'stock' => 10,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => 150000, // Should be saved as number
        ]);
    }
}
