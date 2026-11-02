<?php

declare(strict_types=1);

$runner = new Runner();

$runner->run();

class Runner
{
    public function run(): void
    {
        $inputReader = new InputReader(STDIN);
        $inputValidator = new InputValidator();

        $numberOfTestCases = $inputReader->readSingleInteger();
        $inputValidator->validateNumberOfTestCases($numberOfTestCases);

        $aggressiveCowsSolver = new MinimumDistanceSolver(new BinarySearcher());

        for ($i = 0; $i < $numberOfTestCases; $i++) {
            [$numberOfStalls, $numberOfCows] = $inputReader->readIntegerPair();
            $inputValidator->validateNumberOfCowsAndStalls($numberOfCows, $numberOfStalls);

            $positionsOfStalls = $inputReader->readArrayOfIntegers($numberOfStalls);

            sort($positionsOfStalls, SORT_NUMERIC);
            $inputValidator->validateStallPositions($positionsOfStalls, $numberOfStalls);

            $answer = $aggressiveCowsSolver->calculateMaxiMinDistance($numberOfCows, $positionsOfStalls);
            echo $answer, PHP_EOL;
        }
    }
}

class InputReader
{
    /** @var resource */
    private $inputStream;

    /**
     * @param resource $inputStream
     */
    public function __construct($inputStream) {
        $this->inputStream = $inputStream;
    }
    public function readSingleInteger(): int
    {
        $line = $this->readLine();
        return $this->parseIntegerFromString($line);
    }

    public function readIntegerPair(): array
    {
        $line = $this->readLine();
        $strings = preg_split('/\s+/', $line, 2, PREG_SPLIT_NO_EMPTY);

        if ($strings === false || count($strings) !== 2) {
            throw new UnexpectedValueException('Failed to parse two values from input line ' . $line);
        }

        return [
            $this->parseIntegerFromString($strings[0]),
            $this->parseIntegerFromString($strings[1]),
        ];
    }

    public function readArrayOfIntegers(int $length): array
    {
        $array = [];

        for ($i = 0; $i < $length; $i++) {
            $array[] = $this->readSingleInteger();
        }

        return $array;
    }

    private function readLine(): string
    {
        $line = fgets($this->inputStream);

        if ($line === false) {
            throw new RuntimeException('No input to read');
        }

        return $line;
    }

    private function parseIntegerFromString(string $string): int
    {
        $integer = filter_var(trim($string), FILTER_VALIDATE_INT);

        if ($integer === false) {
            throw new UnexpectedValueException('Integer expected');
        }

        return $integer;
    }
}

class InputValidator
{
    private const MIN_TESTCASES = 1;

    private const MIN_STALLS = 2;
    private const MAX_STALLS = 100000;

    private const MIN_POSITION = 0;
    private const MAX_POSITION = 1000000000;

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

    public function validateStallPositions(array $stallPositions, int $expectedSize): void
    {
        $numberOfStalls = count($stallPositions);

        if ($numberOfStalls !== $expectedSize) {
            throw new UnexpectedValueException('Number of stall positions must be exactly ' . $expectedSize);
        }

        $this->validateNumberOfStalls($numberOfStalls);

        if ($stallPositions[0] < self::MIN_POSITION) {
            throw new UnexpectedValueException('Stall position must be at least ' . self::MIN_POSITION);
        }

        if (end($stallPositions) > self::MAX_POSITION) {
            throw new UnexpectedValueException('Stall position may not be greater than ' . self::MAX_POSITION);
        }
    }
}

class MinimumDistanceSolver
{
    // typed properties allowed only in PHP >=7.4
    private $searcher;

    public function __construct(BinarySearcher $searcher) {
        $this->searcher = $searcher;
    }

    public function calculateMaxiMinDistance(int $numberOfCows, array $positionsOfStalls): int
    {
        $closestStallPosition = $positionsOfStalls[0];
        $furthestStallPosition = end($positionsOfStalls);

        $totalAvailableDistance = $furthestStallPosition - $closestStallPosition;

        // this is a maximal average, rounded down to closest integer
        // meaning, in the most sparse arrangement of cows, there will be neighbouring cows separated by distance not exceeding this one
        $ceilingOfLargestMinimumDistance = intdiv($totalAvailableDistance, $numberOfCows - 1);
        $firstGuaranteedImpossibleDistance = $ceilingOfLargestMinimumDistance + 1;
        $upperBoundExclusive = $firstGuaranteedImpossibleDistance + 1;

        $firstImpossibleDistance = $this->searcher->findFirstTrue(0, $upperBoundExclusive, function (int $distance) use ($numberOfCows, $positionsOfStalls) {
            return !$this->isMinimumDistancePossible($numberOfCows, $positionsOfStalls, $distance);
        });

        return $firstImpossibleDistance - 1;
    }

    private function isMinimumDistancePossible(int $numberOfCows, array $positionsOfStalls, int $minimumDistance): bool
    {
        $nextAllowedStallIndex = 0;

        for ($i = 1; $i < $numberOfCows; $i++) {
            $nextAllowedStallIndex = $this->findNextAllowedStallIndex($positionsOfStalls, $nextAllowedStallIndex, $minimumDistance);

            if ($nextAllowedStallIndex === -1) {
                return false; // could not be found
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

        return $this->searcher->findFirstTrue($rightmostOccupiedStallIndex, count($positionsOfStalls), function ($index) use ($positionsOfStalls, $minimumNeededPosition) {
            return $positionsOfStalls[$index] >= $minimumNeededPosition;
        });
    }
}

class BinarySearcher
{
    public function findFirstTrue(int $lowerBoundInclusive, int $upperBoundExclusive, callable $testPredicate): int
    {
        $left = $lowerBoundInclusive;
        $right = $upperBoundExclusive;

        while ($left < $right) {
            $middle = $left + intdiv($right - $left, 2);

            if ($testPredicate($middle)) {
                $right = $middle;
            } else {
                $left = $middle + 1;
            }
        }

        if ($left < $upperBoundExclusive) {
            return $left;
        }

        // meaning, nothing tested positive
        return -1;
    }
}
