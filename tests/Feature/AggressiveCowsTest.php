<?php

use Symfony\Component\Process\Process;

beforeEach(function () {
    $this->setUpProcess = static function (array $streamContents) {
        $projectDirectory = dirname(__DIR__, 2);

        $phpProcess = new Process(
            [PHP_BINARY, 'aggressive_cows.php'],
            $projectDirectory
        );

        $phpProcess->setInput(implode(PHP_EOL, $streamContents));

        // as per SPOJ conditions
        $phpProcess->setTimeout(2);

        $phpProcess->run();

        return $phpProcess;
    };
});

it('solves the sample case through stdin and stdout', function () {
    $process = ($this->setUpProcess)(['1', '5 3', '1', '2', '4', '8', '9']);

    expect($process->isSuccessful())->toBeTrue()
        ->and($process->getOutput())->toBe("3\n")
        ->and($process->getErrorOutput())->toBe('');
});

it('solves with multiple testcases', function () {
    $inputStreamContents = [
        '5',
        ...['5 3', '0', '2', '4', '8', '9'],
        ...['5 3', '1', '2', '4', '8', '9'],
        ...['5 3', '9', '8', '4', '2', '1'],
        ...['4 3', '10', '1000', '1005', '15'],
        ...['20 10', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']
    ];

    $process = ($this->setUpProcess)($inputStreamContents);

    expect($process->isSuccessful())->toBeTrue()
        ->and($process->getOutput())->toBe(implode(PHP_EOL, ['4', '3', '3', '5', '2']) . PHP_EOL)
        ->and($process->getErrorOutput())->toBe('');
});

it('fails with the input stream contents different than the contract specifies', function (
    array $streamContents,
    string $expectedExceptionMessage
) {
    $process = ($this->setUpProcess)($streamContents);

    expect($process->isSuccessful())->toBeFalse()
        ->and($process->getErrorOutput())->toContain($expectedExceptionMessage);
})->with([
    'empty stream' => [[], 'No input to read'],
    'stalls less than promised' => [['1', '3 2', '1', '2'], 'No input to read'],
    'no double-integer line' => [['1', '3', '2', '1', '2'], 'Failed to parse two values from input line'],
    'zero testcases' => [['0'], 'Number of test cases must be at least 1'],
    'negative stall positions' => [['1', '5 2', '-1', '-2', '-3', '-4', '-5'], 'Stall position must be at least 0'],
]);
