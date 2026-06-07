<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Category;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\Transaction;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryStockTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helper ────────────────────────────────────────────────────────────────
    protected function authenticatedSession(): array
    {
        return ['pos_authenticated' => true];
    }

    // ─── Test 1: Transaction deducts and void restores stock ──────────────────
    public function test_transaction_deducts_and_void_restores_stock(): void
    {
        // Setup
        $category = Category::create(['name' => 'Makanan', 'sort_order' => 1]);

        $menu = Menu::create([
            'name'        => 'Kebab Sapi Premium',
            'category_id' => $category->id,
            'price'       => 20000,
            'is_active'   => true,
        ]);

        $daging   = RawMaterial::create(['name' => 'Daging Sapi TX1',   'stock' => 100.00, 'min_stock' => 10.00, 'unit' => 'gr']);
        $tortilla = RawMaterial::create(['name' => 'Tortilla Kebab TX1', 'stock' => 50.00,  'min_stock' => 5.00,  'unit' => 'pcs']);

        Recipe::create(['menu_id' => $menu->id, 'raw_material_id' => $daging->id,   'quantity' => 45.00]);
        Recipe::create(['menu_id' => $menu->id, 'raw_material_id' => $tortilla->id, 'quantity' => 1.00]);

        // POST /transactions — qty=2
        $response = $this->withSession($this->authenticatedSession())
            ->postJson('/transactions', [
                'channel'        => 'Langsung',
                'payment_method' => 'Tunai',
                'cart'           => [
                    ['id' => $menu->id, 'name' => $menu->name, 'price' => $menu->price, 'quantity' => 2],
                ],
            ]);

        $response->assertStatus(200)->assertJsonPath('success', true);

        // Daging: 100 - (45 * 2) = 10  |  Tortilla: 50 - (1 * 2) = 48
        $this->assertEquals(10.00, (float) $daging->fresh()->stock,   'Daging stock should be 10 after 2 orders');
        $this->assertEquals(48.00, (float) $tortilla->fresh()->stock,  'Tortilla stock should be 48 after 2 orders');

        // DELETE /transactions/{id} — void restores stock
        $transactionId = $response->json('transaction_id');
        $deleteResponse = $this->withSession($this->authenticatedSession())
            ->delete('/transactions/' . $transactionId);

        $deleteResponse->assertSessionHasNoErrors();
        $this->assertFalse($deleteResponse->isServerError(), 'Void transaction returned a server error');

        // Stock should be restored
        $this->assertEquals(100.00, (float) $daging->fresh()->stock,   'Daging stock should be restored to 100');
        $this->assertEquals(50.00,  (float) $tortilla->fresh()->stock,  'Tortilla stock should be restored to 50');
    }

    // ─── Test 2: Purchase increments and delete decrements stock ─────────────
    public function test_purchase_increments_and_delete_decrements_stock(): void
    {
        $daging = RawMaterial::create([
            'name'      => 'Daging Sapi PU1',
            'stock'     => 10.00,
            'min_stock' => 5.00,
            'unit'      => 'gr',
        ]);

        // POST /management/purchases — note: controller validates 'date' (not 'purchase_date')
        $response = $this->withSession($this->authenticatedSession())
            ->post('/management/purchases', [
                'date'         => now()->toDateString(),
                'total_amount' => 50000,
                'items'        => [
                    [
                        'name'            => 'Belanja Daging Sapi',
                        'raw_material_id' => $daging->id,
                        'qty'             => 50,
                        'price'           => 1000,
                    ],
                ],
            ]);

        // Controller uses back() → assertRedirect without specific URL
        $response->assertSessionHasNoErrors();
        $this->assertFalse($response->isServerError(), 'Purchase store returned a server error: ' . $response->getContent());

        // Stock: 10 + 50 = 60
        $this->assertEquals(60.00, (float) $daging->fresh()->stock, 'Stock should be 60 after purchase');

        // DELETE /management/purchases/{id}
        $purchase = Purchase::latest()->first();
        $this->assertNotNull($purchase, 'Purchase should have been created');

        $deleteResponse = $this->withSession($this->authenticatedSession())
            ->delete('/management/purchases/' . $purchase->id);

        $deleteResponse->assertSessionHasNoErrors();

        // Stock: 60 - 50 = 10 (restored)
        $this->assertEquals(10.00, (float) $daging->fresh()->stock, 'Stock should be restored to 10 after purchase deletion');
    }

    // ─── Test 3: Low stock alert endpoint ────────────────────────────────────
    public function test_low_stock_alert_endpoint_returns_correct_items(): void
    {
        RawMaterial::create(['name' => 'Bahan Aman LS1',   'stock' => 100.00, 'min_stock' => 10.00,  'unit' => 'gr']);
        RawMaterial::create(['name' => 'Bahan Menipis LS2', 'stock' => 3.00,   'min_stock' => 10.00,  'unit' => 'pcs']);
        RawMaterial::create(['name' => 'Bahan Habis LS3',   'stock' => 0.00,   'min_stock' => 5.00,   'unit' => 'ml']);

        $response = $this->withSession($this->authenticatedSession())
            ->getJson('/management/stock/low-alerts');

        $response->assertStatus(200)
            ->assertJsonPath('count', 2);  // only 2 are at/below min_stock

        $names = collect($response->json('items'))->pluck('name')->toArray();
        $this->assertContains('Bahan Menipis LS2', $names);
        $this->assertContains('Bahan Habis LS3',   $names);
        $this->assertNotContains('Bahan Aman LS1', $names);
    }
}
