<?php

declare(strict_types=1);

use AggressiveCows\Solver\BinarySearcher;

beforeEach(function () {
    $this->binarySearcher = new BinarySearcher();
});

describe('findFirstTrue', function () {
    it('finds correct index', function (array $array, int $expectedIndex) {
        $indexFound = $this->binarySearcher->findFirstTrue(
            lowerBoundInclusive: 0,
            upperBoundExclusive: count($array),
            testPredicate: fn (int $index) => $array[$index] > 5
        );

        expect($indexFound)->toBe($expectedIndex);
    })->with([
        'single element' => [[6], 0],
        'first element' => [[6, 7, 8, 9, 10], 0],
        'first of multiple equal elements' => [[9, 9, 9, 9], 0],
        'second element' => [[5, 6, 7, 8, 9, 10], 1],
        'last element' => [[1, 2, 3, 4, 5, 6], 5],
        'single at the middle' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 5],
        'first of multiple equal at the middle' => [[5, 5, 5, 6, 6, 6, 7, 7, 7, 8], 3],
    ]);

    it('returns -1 when nothing reports true', function (array $array) {
        $indexFound = $this->binarySearcher->findFirstTrue(
            lowerBoundInclusive: 0,
            upperBoundExclusive: count($array),
            testPredicate: fn (int $index) => $array[$index] > 1000
        );

        expect($indexFound)->toBe(-1);
    })->with([
        'empty' => [[]],
        'single failing test' => [[1]],
        'multiple failing the test' => [[1, 2, 3, 4, 5]],
        'closest failing the test at the boundary' => [[800, 900, 999, 1000]],
        'single element array failing test at the boundary' => [[1000]],
        'multiple equal elements failing test at the boundary' => [[1000, 1000, 1000, 1000]],
    ]);

    it('returns -1 with empty range', function () {
        $indexFound = $this->binarySearcher->findFirstTrue(
            lowerBoundInclusive: 5,
            upperBoundExclusive: 5,
            testPredicate: fn (int $index) => true
        );

        expect($indexFound)->toBe(-1);
    });

    it('finds answer with nonzero lower bound', function (
        int $lowerBound,
        int $upperBound,
        int $expectedResult
    ) {
        $indexFound = $this->binarySearcher->findFirstTrue(
            lowerBoundInclusive: $lowerBound,
            upperBoundExclusive: $upperBound,
            testPredicate: fn (int $element) => $element > 100
        );

        expect($indexFound)->toBe($expectedResult);
    })->with([
        'base case' => [90, 110, 101],
        'first in range matches' => [101, 200, 101],
        'does not pass the test at the boundary' => [90, 100, -1],
        'passes test but equal to noninclusive bound' => [90, 101, -1],
    ]);
});
