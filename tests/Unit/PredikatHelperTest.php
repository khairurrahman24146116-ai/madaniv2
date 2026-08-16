<?php

namespace Tests\Unit;

use App\Services\PredikatHelper;
use PHPUnit\Framework\TestCase;

class PredikatHelperTest extends TestCase
{
    public function test_dari_nilai_mendapatkan_predikat_yang_tepat(): void
    {
        $this->assertSame('A', PredikatHelper::dariNilai(100));
        $this->assertSame('A', PredikatHelper::dariNilai(90));
        $this->assertSame('B', PredikatHelper::dariNilai(89.99));
        $this->assertSame('B', PredikatHelper::dariNilai(80));
        $this->assertSame('C', PredikatHelper::dariNilai(79.99));
        $this->assertSame('C', PredikatHelper::dariNilai(70));
        $this->assertSame('D', PredikatHelper::dariNilai(69.99));
        $this->assertSame('D', PredikatHelper::dariNilai(0));
    }
}
