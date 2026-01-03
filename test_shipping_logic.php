<?php
/**
 * Test logic tính phí vận chuyển
 * 
 * Chạy: php test_shipping_logic.php
 */

// Test công thức: Phí = F + (q - 1) × F × r
// Với r = (100 - discount_percent) / 100

echo "=== TEST LOGIC TÍNH PHÍ VẬN CHUYỂN ===\n\n";

// Test Case 1: F = 100,000đ, discount = 50%, q = 4
echo "Test Case 1: F = 100,000đ, discount = 50%, q = 4\n";
$F = 100000;
$discount_percent = 50;
$q = 4;
$r = (100 - $discount_percent) / 100; // 0.5
$fee = $F + (($q - 1) * $F * $r);
echo "  r = (100 - {$discount_percent}) / 100 = {$r}\n";
echo "  Phí = {$F} + ({$q} - 1) × {$F} × {$r}\n";
echo "  Phí = {$F} + " . (($q - 1) * $F * $r) . "\n";
echo "  Phí = {$fee}đ\n";
echo "  Kết quả mong đợi: 250,000đ\n";
echo "  " . ($fee == 250000 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test Case 2: F = 60,000đ, discount = 10%, q = 3
echo "Test Case 2: F = 60,000đ, discount = 10%, q = 3\n";
$F = 60000;
$discount_percent = 10;
$q = 3;
$r = (100 - $discount_percent) / 100; // 0.9
$fee = $F + (($q - 1) * $F * $r);
echo "  r = (100 - {$discount_percent}) / 100 = {$r}\n";
echo "  Phí = {$F} + ({$q} - 1) × {$F} × {$r}\n";
echo "  Phí = {$F} + " . (($q - 1) * $F * $r) . "\n";
echo "  Phí = {$fee}đ\n";
echo "  Kết quả mong đợi: 168,000đ\n";
echo "  " . ($fee == 168000 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test Case 3: q = 1 (không giảm giá)
echo "Test Case 3: F = 100,000đ, q = 1 (không giảm giá)\n";
$F = 100000;
$q = 1;
$fee = $F * $q; // Không áp dụng giảm giá
echo "  Phí = {$F} × {$q} = {$fee}đ\n";
echo "  Kết quả mong đợi: 100,000đ\n";
echo "  " . ($fee == 100000 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test Case 4: discount = 0% (không giảm)
echo "Test Case 4: F = 100,000đ, discount = 0%, q = 3\n";
$F = 100000;
$discount_percent = 0;
$q = 3;
$r = (100 - $discount_percent) / 100; // 1.0
$fee = $F + (($q - 1) * $F * $r);
echo "  r = (100 - {$discount_percent}) / 100 = {$r}\n";
echo "  Phí = {$F} + ({$q} - 1) × {$F} × {$r}\n";
echo "  Phí = {$F} + " . (($q - 1) * $F * $r) . "\n";
echo "  Phí = {$fee}đ\n";
echo "  Kết quả mong đợi: 300,000đ (không giảm)\n";
echo "  " . ($fee == 300000 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test Case 5: Nhiều sản phẩm
echo "Test Case 5: Nhiều sản phẩm\n";
echo "  Sản phẩm A: F = 100,000đ, discount = 50%, q = 4\n";
$F_A = 100000;
$discount_A = 50;
$q_A = 4;
$r_A = (100 - $discount_A) / 100;
$fee_A = $F_A + (($q_A - 1) * $F_A * $r_A);
echo "    Phí A = {$fee_A}đ\n";

echo "  Sản phẩm B: F = 60,000đ, discount = 10%, q = 3\n";
$F_B = 60000;
$discount_B = 10;
$q_B = 3;
$r_B = (100 - $discount_B) / 100;
$fee_B = $F_B + (($q_B - 1) * $F_B * $r_B);
echo "    Phí B = {$fee_B}đ\n";

$total = $fee_A + $fee_B;
echo "  Tổng phí = {$fee_A} + {$fee_B} = {$total}đ\n";
echo "  Kết quả mong đợi: 418,000đ\n";
echo "  " . ($total == 418000 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "=== KẾT THÚC TEST ===\n";

