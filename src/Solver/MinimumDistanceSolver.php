<?php

declare(strict_types=1);

namespace AggressiveCows\Solver;

class MinimumDistanceSolver
{
    public function __construct(
        private readonly BinarySearcher $searcher
    ) {
    }

    /**
     * NB! This assumes that the positions array is
     * - sorted,
     * - has consecutive integer keys, starting with zero
     * - contains at least $numberOfCows entries
     *
     * This also assumes that there will be at least 2 cows.
     *
     * We do not check these assumptions here.
     * (Input is being validated, just not here)
     * @param array<int> $positionsOfStalls
     */
    public function calculateMaxiMinDistance(int $numberOfCows, array $positionsOfStalls): int
    {
        $closestStallPosition = $positionsOfStalls[0];
        $furthestStallPosition = end($positionsOfStalls);

        $totalAvailableDistance = $furthestStallPosition - $closestStallPosition;

        // this is a maximal average, rounded down to closest integer
        // meaning, in the most sparse arrangement of cows, there will be neighbouring cows
        // separated by distance not exceeding this one
        $ceilingOfLargestMinimumDistance = intdiv($totalAvailableDistance, $numberOfCows - 1);
        $firstGuaranteedImpossibleDistance = $ceilingOfLargestMinimumDistance + 1;
        $upperBoundExclusive = $firstGuaranteedImpossibleDistance + 1;

        $firstImpossibleDistance = $this->searcher->findFirstTrue(
            lowerBoundInclusive: 0,
            upperBoundExclusive: $upperBoundExclusive,
            testPredicate: fn (int $distance) => !$this->isMinimumDistancePossible(
                $numberOfCows,
                $positionsOfStalls,
                $distance
            )
        );

        return $firstImpossibleDistance - 1;
    }

    private function isMinimumDistancePossible(int $numberOfCows, array $positionsOfStalls, int $minimumDistance): bool
    {
        $nextAllowedStallIndex = 0;

        for ($i = 1; $i < $numberOfCows; $i++) {
            $nextAllowedStallIndex = $this->findNextAllowedStallIndex(
                $positionsOfStalls,
                $nextAllowedStallIndex,
                $minimumDistance
            );

            // needed distance too large, no stalls left
            if ($nextAllowedStallIndex === -1) {
                return false;
            }
        }

        return true;
    }

    private function findNextAllowedStallIndex(
        array $positionsOfStalls,
        int $rightmostOccupiedStallIndex,
        int $minimumDistance
    ): int {

        $rightmostOccupiedStallPosition = $positionsOfStalls[$rightmostOccupiedStallIndex];
        $minimumNeededPosition = $rightmostOccupiedStallPosition + $minimumDistance;

        return $this->searcher->findFirstTrue(
            lowerBoundInclusive: $rightmostOccupiedStallIndex,
            upperBoundExclusive: count($positionsOfStalls),
            testPredicate: fn ($index) => $positionsOfStalls[$index] >= $minimumNeededPosition
        );
    }
}
