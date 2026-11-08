<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Keeps the cafe map styles in the cafes entry. If these rules drift back
 * into app.css — or the import moves before Leaflet's CSS — /cafes loses
 * its map skin without any PHP test noticing.
 */
class CafeAssetTest extends TestCase
{
    public function test_map_styles_stay_in_the_cafes_entry(): void
    {
        $app = file_get_contents(resource_path('css/app.css'));
        $cafes = file_get_contents(resource_path('css/cafes.css'));
        $js = file_get_contents(resource_path('js/cafes.js'));

        // The shared pause hook stays global; the map rules do not.
        $this->assertStringContainsString('[data-loop].is-paused .cafe-progress', $app);
        $this->assertStringNotContainsString('.cafe-popup', $app);
        $this->assertStringNotContainsString('.cafe-pin', $app);
        $this->assertStringContainsString('.cafe-popup', $cafes);

        // cafes.css must load after Leaflet's own stylesheets.
        $leaflet = strpos($js, "import 'leaflet.markercluster/dist/MarkerCluster.css';");
        $mapCss = strpos($js, "import '../css/cafes.css';");

        $this->assertNotFalse($leaflet);
        $this->assertNotFalse($mapCss);
        $this->assertGreaterThan($leaflet, $mapCss);
    }
}
