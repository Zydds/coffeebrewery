<?php

namespace Tests\Unit;

use App\Models\Recipe;
use PHPUnit\Framework\TestCase;

class RecipeScaleInfoTest extends TestCase
{
    public function test_filter_ratio_scales_from_15g(): void
    {
        $recipe = new Recipe(['ratio' => '1:15']);

        $this->assertSame(['coffee_g' => 15, 'water_ml' => 225], $recipe->scaleInfo());
    }

    public function test_espresso_ratio_scales_from_18g(): void
    {
        $recipe = new Recipe(['ratio' => '1:2']);

        $this->assertSame(['coffee_g' => 18, 'water_ml' => 36], $recipe->scaleInfo());

        $recipe->ratio = '1:2.2';
        $this->assertSame(['coffee_g' => 18, 'water_ml' => 40], $recipe->scaleInfo());
    }

    public function test_free_text_ratio_is_not_scalable(): void
    {
        $recipe = new Recipe(['ratio' => '1 sachet : 150 ml']);

        $this->assertNull($recipe->scaleInfo());
    }
}
