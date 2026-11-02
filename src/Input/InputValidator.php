<?php

declare(strict_types=1);

namespace AggressiveCows\Input;

use UnexpectedValueException;

class InputValidator
{
    private const int MIN_TESTCASES = 1;

    private const int MIN_STALLS = 2;
    private const int MAX_STALLS = 100_000;

    private const int MIN_POSITION = 0;
    private const int MAX_POSITION = 1_000_000_000;

    public function validateNumberOfTestCases(int $numberOfTestCases): void
    {
        if ($numberOfTestCases < self::MIN_TESTCASES) {
            throw new UnexpectedValueException('Number of test cases must be at least 1');
        }
    }

    public function validateNumberOfCowsAndStalls(int $numberOfCows, int $numberOfStalls): void
    {
        if ($numberOfCows < self::MIN_STALLS) {
            throw new UnexpectedValueException('Not enough cows');
        }

        if ($numberOfCows > $numberOfStalls) {
            throw new UnexpectedValueException('Number of cows must not exceed the number of stalls');
        }

        $this->validateNumberOfStalls($numberOfStalls);
    }

    private function validateNumberOfStalls(int $numberOfStalls): void
    {
        if ($numberOfStalls < self::MIN_STALLS) {
            throw new UnexpectedValueException('Not enough stalls');
        }

        if ($numberOfStalls > self::MAX_STALLS) {
            throw new UnexpectedValueException('Too much stalls');
        }
    }

    /**
     * NB! This assumes the array is sorted. We are not checking that assumption here because that would make this
     * method O(n)
     */
    public function validateStallPositions(array $stallPositions, int $expectedSize): void
    {
        $numberOfStalls = count($stallPositions);

        if ($numberOfStalls !== $expectedSize) {
            throw new UnexpectedValueException('Number of stall positions must be exactly ' . $expectedSize);
        }

        $this->validateNumberOfStalls($numberOfStalls);

        // these two checks assume the array is sorted
        if ($stallPositions[0] < self::MIN_POSITION) {
            throw new UnexpectedValueException('Stall position must be at least ' . self::MIN_POSITION);
        }

        if (end($stallPositions) > self::MAX_POSITION) {
            throw new UnexpectedValueException('Stall position may not be greater than ' . self::MAX_POSITION);
        }
    }
}
