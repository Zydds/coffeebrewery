<?php

namespace App\Services\Cafes;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Minimal Overpass API client with multi-instance failover.
 *
 * The public instance overpass-api.de is unreachable from the dev machine
 * (its TLS handshake dies there) and mirrors rate-limit or return 504 under
 * load, so the importer cycles through config('cafes.overpass_instances')
 * with linear backoff instead of trusting a single endpoint.
 */
class OverpassClient
{
    /**
     * Run an Overpass QL query and return the decoded JSON array
     * (['elements' => [...], 'osm3s' => [...], ...]).
     *
     * @throws RuntimeException when every instance and retry fails
     */
    public function query(string $overpassQl): array
    {
        /** @var list<string> $instances */
        $instances = config('cafes.overpass_instances');
        $retries = (int) config('cafes.overpass_retries_per_instance', 2);
        $backoff = (int) config('cafes.overpass_backoff_seconds', 5);
        $timeout = (int) config('cafes.overpass_timeout_seconds', 180);

        $errors = [];
        $last = count($instances) - 1;

        foreach ($instances as $i => $instance) {
            for ($try = 1; $try <= $retries; $try++) {
                try {
                    $response = Http::asForm()
                        ->timeout($timeout)
                        // Overpass rejects unidentified clients with HTTP 406;
                        // its usage policy requires a User-Agent that
                        // identifies the application (and a contact point).
                        ->withHeaders([
                            'User-Agent' => config('cafes.user_agent'),
                            'Accept' => 'application/json',
                        ])
                        ->post($instance, ['data' => $overpassQl]);
                } catch (ConnectionException $e) {
                    $response = null;
                    $errors[] = "{$instance}: {$e->getMessage()}";
                }

                if ($response !== null) {
                    if ($response->successful()) {
                        $decoded = $response->json();

                        // Rate-limit or error pages can come back as 200 with
                        // an unexpected shape; only a real elements payload
                        // counts as success.
                        if (is_array($decoded) && isset($decoded['elements'])) {
                            return $decoded;
                        }

                        $errors[] = "{$instance}: response has no elements";
                    } else {
                        $errors[] = "{$instance}: HTTP {$response->status()}";
                    }
                }

                // Linear backoff between attempts (2s, 4s, ...); skip the
                // sleep after the very last attempt — nothing left to protect.
                if (! ($i === $last && $try === $retries)) {
                    sleep($backoff * $try);
                }
            }
        }

        Log::error('Overpass query failed on every instance', ['errors' => $errors]);

        throw new RuntimeException('Overpass API unreachable on all instances: '.implode('; ', $errors));
    }
}
