<?php

declare(strict_types=1);

namespace AggressiveCows\Runner;

use AggressiveCows\Input\InputReader;
use AggressiveCows\Input\InputValidator;
use AggressiveCows\Solver\BinarySearcher;
use AggressiveCows\Solver\MinimumDistanceSolver;

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
