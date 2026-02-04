<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\SKU;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SKUTest extends TestCase
{
    public function test_can_create_valid_sku(): void
    {
        $sku = new SKU('ABC-123');
        
        $this->assertEquals('ABC-123', $sku->value());
    }

    public function test_sku_is_uppercase(): void
    {
        $sku = new SKU('abc-123');
        
        $this->assertEquals('ABC-123', $sku->value());
    }

    public function test_sku_trims_whitespace(): void
    {
        $sku = new SKU('  ABC-123  ');
        
        $this->assertEquals('ABC-123', $sku->value());
    }

    public function test_cannot_create_empty_sku(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        new SKU('');
    }

    public function test_cannot_create_sku_with_special_chars(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        new SKU('ABC@123');
    }

    public function test_cannot_exceed_max_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        new SKU(str_repeat('A', 51));
    }

    public function test_sku_equality(): void
    {
        $sku1 = new SKU('ABC-123');
        $sku2 = new SKU('abc-123');
        $sku3 = new SKU('XYZ-999');
        
        $this->assertTrue($sku1->equals($sku2));
        $this->assertFalse($sku1->equals($sku3));
    }

    public function test_sku_to_string(): void
    {
        $sku = new SKU('ABC-123');
        
        $this->assertEquals('ABC-123', (string) $sku);
    }

    public function test_sku_with_underscores_and_hyphens(): void
    {
        $sku = new SKU('ABC_123-DEF');
        
        $this->assertEquals('ABC_123-DEF', $sku->value());
    }
}
