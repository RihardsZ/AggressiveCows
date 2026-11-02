<?php

declare(strict_types=1);

use AggressiveCows\Solver\BinarySearcher;
use AggressiveCows\Solver\MinimumDistanceSolver;

beforeEach(function () {
    $this->solver = new MinimumDistanceSolver(
        new BinarySearcher()
    );
});

describe('MinimumDistanceSolver', function () {

    it('solves the example problem', function (
        int $numberOfCows,
        array $positionsOfStalls,
        int $expectedMinimumDistance
    ) {
        $distance = $this->solver->calculateMaxiMinDistance($numberOfCows, $positionsOfStalls);

        expect($distance)->toBe($expectedMinimumDistance);
    })->with([
        'SPOJ example' => [3, [1, 2, 4, 8, 9], 3],
        'a bit longer example' => [7, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20], 3],
        'SPOJ example, max cows' => [5, [1, 2, 4, 8, 9], 1],
        'more stalls, max cows' => [20, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20], 1],
        'SPOJ example, min cows' => [2, [1, 2, 4, 8, 9], 8],
        'more stalls, min cows' => [2, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20], 19],
        'max stalls, min cows' => [2, range(0, 1_000_000_000, 10_000), 1_000_000_000],
        'max stalls, some cows' => [11, range(0, 1_000_000_000, 10_000), 100_000_000],
        'just enough stalls, small position' => [2, [0, 1], 1],
        'just enough stalls, large position' => [2, [0, 1_000_000_000], 1_000_000_000],
        'intervals differing from average interval length' => [3, [10, 15, 1000, 1005], 5],
        'all stalls in same positon' => [3, [5, 5, 5], 0],
        'some stalls in same position' => [3, [5, 5, 15], 0],
    ]);
});
