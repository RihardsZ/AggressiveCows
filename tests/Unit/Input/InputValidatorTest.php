<?php

use AggressiveCows\Input\InputValidator;

beforeEach(function () {
    $this->inputValidator = new InputValidator();
});

describe('validateNumberOfTestCases', function () {
    it('allows integer values not less than 1', function (int $numberOfTestCases) {
        $this->inputValidator->validateNumberOfTestCases($numberOfTestCases);
    })->with([
        'single' => [1],
        'multiple' => [123],
        'max' => [PHP_INT_MAX],
    ])->throwsNoExceptions();

    it('throws with integer values less than 1', function (int $numberOfTestCases) {
        $this->inputValidator->validateNumberOfTestCases($numberOfTestCases);
    })->with([
        'no cases' => [0],
        'negative one cases' => [-1],
        'negative more than one case' => [-123],
        'max number of negative cases' => [PHP_INT_MIN],
    ])
    ->throws(UnexpectedValueException::class);
});

describe('validateNumberOfCowsAndStalls', function () {
    it('allows greater greater or equal number of stalls', function (int $numberOfCows, int $numberOfStalls) {
        $this->inputValidator->validateNumberOfCowsAndStalls($numberOfCows, $numberOfStalls);
    })->with([
        'min same' => [2, 2],
        'min small' => [2, 3],
        'min mid' => [2, 100],
        'min max' => [2, 100_000],
        'mid same' => [100, 100],
        'mid mid' => [100, 500],
        'mid max' => [100, 100_000],
        'both maximum' => [100_000, 100_000],
    ])->throwsNoExceptions();

    it('throws when cows or stalls are less than 2', function (int $numberOfCows, int $numberOfStalls) {
        $this->inputValidator->validateNumberOfCowsAndStalls($numberOfCows, $numberOfStalls);
    })->with([
        'one cow' => [1, 2],
        'zero cows' => [0, 2],
        'negative one cows' => [-1, 2],
        'negative mid cows' => [-123, 2],
        'negative max cows' => [PHP_INT_MIN, 2],
        'one stall' => [2, 1],
        'zero stalls' => [2, 0],
        'negative one stalls' => [2, -1],
        'negative mid stalls' => [2, -123],
        'negative max stalls' => [2, PHP_INT_MIN],
        'both zero' => [0, 0],
        'both one' => [1, 1],
        'both negative' => [-1, -1],
    ])->throws(UnexpectedValueException::class);

    it('throws when there is more than 100 000 stalls', function (int $numberOfCows, int $numberOfStalls) {
        $this->inputValidator->validateNumberOfCowsAndStalls($numberOfCows, $numberOfStalls);
    })->with([
        'just over the limit' => [2, 100_001],
        'way over the limit' => [2, PHP_INT_MAX],
    ])->throws(UnexpectedValueException::class);

    it('throws when there are more cows than there are stalls', function (int $numberOfCows, int $numberOfStalls) {
        $this->inputValidator->validateNumberOfCowsAndStalls($numberOfCows, $numberOfStalls);
    })->with([
        'just over the number of few stalls' => [3, 2],
        'way over the number of few stalls' => [10_000, 2],
        'just over the number of lots of stalls' => [10_000, 9999],
        'way over the number of lots of stalls' => [PHP_INT_MAX, 9999],
    ])->throws(UnexpectedValueException::class);
});

describe('validateStallPositions', function () {
    it('allows valid input', function (array $stallPositions, int $count) {
        $this->inputValidator->validateStallPositions($stallPositions, $count);
    })->with([
        'just at the very ends' => [[0, 1_000_000_000], 2],
        'at the ends and in the middle' => [[0, 5, 1_000_000_000], 3],
        'maximum number of stalls' => [range(1, 100_000), 100_000],
        'stalls in the middle' => [[100, 2000, 30_000], 3]
    ])->throwsNoExceptions();

    it('throws on invalid input', function (array $stallPositions, int $count) {
        $this->inputValidator->validateStallPositions($stallPositions, $count);
    })->with([
        'no actual stalls' => [[], 0],
        'just one stall' => [[5], 1],
        'promised and actual number of stalls differ' => [[0, 1_000_000_000], 5],
        'negative stall position' => [[-1, 1_000_000_000], 2],
        'too large stall position' => [[0, 1_000_000_001], 2],
        'too large number of stalls' => [range(1, 100_001), 100_001]
    ])->throws(UnexpectedValueException::class);
});
