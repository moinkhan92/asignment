<?php

namespace App\Services;

class DataStreamAnalyzer
{
    public function analyze(string $stream, int $k, int $top, array $exclude = []): array
    {
        $counts = [];
        $excludedSet = array_flip($exclude); // Optimize exclude lookup

        // Sliding window algorithm
        for ($i = 0; $i <= strlen($stream) - $k; $i++) {
            $substring = substr($stream, $i, $k);

            // Skip excluded subsequences
            if (isset($excludedSet[$substring])) {
                continue;
            }

            // Count subsequences
            if (!isset($counts[$substring])) {
                $counts[$substring] = 0;
            }
            $counts[$substring]++;
        }

        // Sort by frequency (descending) and alphabetically for ties
        arsort($counts);

        // Return the top N results
        $result = [];
        foreach (array_slice($counts, 0, $top, true) as $subsequence => $count) {
            $result[] = ['subsequence' => $subsequence, 'count' => $count];
        }

        return $result;
    }
}