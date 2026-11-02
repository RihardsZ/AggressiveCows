<?php

use AggressiveCows\Input\InputReader;

beforeEach(function () {
    $this->inputStream = fopen('php://memory', 'r+');

    $this->inputReader = new InputReader($this->inputStream);
});

describe('readSingleInteger', function () {
    it('reads single integer from the input stream', function (
        string $inputStreamContents,
        int $expectedResult
    ) {
        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);

        $actualResult = $this->inputReader->readSingleInteger();
        expect($actualResult)->toBe($expectedResult);
    })->with([
        'base case' => ['5', 5],
        'whitespace' => [' 5 ', 5],
        'negative' => [' -5 ', -5],
        'zero' => [' 0', 0],
        'negative zero' => [' -0', 0],
        'positive zero' => [' +0', 0],
        'large negative' => ['-123456789', -123456789],
        'large positive' => ['987654321', 987654321],
    ]);

    it('reads successive integers from the input stream', function () {
        $inputStreamContents = <<<'INPUT'
1
10
123456789
-123456789
 0 
   -1     
17
INPUT;

        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);

        expect($this->inputReader->readSingleInteger())->toBe(1)
            ->and($this->inputReader->readSingleInteger())->toBe(10)
            ->and($this->inputReader->readSingleInteger())->toBe(123456789)
            ->and($this->inputReader->readSingleInteger())->toBe(-123456789)
            ->and($this->inputReader->readSingleInteger())->toBe(0)
            ->and($this->inputReader->readSingleInteger())->toBe(-1)
            ->and($this->inputReader->readSingleInteger())->toBe(17);
    });

    it('throws an RuntimeException when there\'s nothing in the input stream', function () {
        fwrite($this->inputStream, '');
        rewind($this->inputStream);
        $this->inputReader->readSingleInteger();
    })->throws(RuntimeException::class);

    it('throws an UnexpectedValueException when the input stream has something other than a single integer', function (
        string $inputStreamContents
    ) {
        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);
        $this->inputReader->readSingleInteger();
    })
        ->with([
            'whitespace only' => [' '],
            'non-numeric string' => ['aabbcc'],
            'float' => ['12.85'],
            'comma-separated' => ['12,85'],
            'space-separated' => ['12 85'],
        ])
        ->throws(UnexpectedValueException::class);
});

describe('readIntegerPair', function () {
    it('reads a pair of integers from the input stream', function (
        string $inputStreamContents,
        int $expectedFirstInteger,
        int $expectedSecondInteger
    ) {
        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);

        [$actualFirst, $actualSecond] = $this->inputReader->readIntegerPair();
        expect($actualFirst)->toBe($expectedFirstInteger)
            ->and($actualSecond)->toBe($expectedSecondInteger);
    })->with([
        'base case' => ['5 8', 5, 8],
        'whitespace present around' => [' 5 8 ', 5, 8],
        'negative pair' => [' -5 -8 ', -5, -8],
        'pair includes zero' => [' 0 1', 0, 1],
        'par includes zero the other way round' => ['1 0 ', 1, 0],
        'lots of whitespace' => ['       1     2         ', 1, 2],
        'pair includes negative number' => ['-123456789 6482048', -123456789, 6482048],
        'pair includes negative number the other way round' => ['6482048 -987654321', 6482048, -987654321],
    ]);

    it('reads successive integer pairs from the input stream', function () {
        $inputStreamContents = <<<'INPUT'
1 2
2 10
123456789 2
-123456789 987654321
 0      0       
   -1     17        
17    +0
INPUT;

        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);

        expect($this->inputReader->readIntegerPair())->toBe([1, 2])
            ->and($this->inputReader->readIntegerPair())->toBe([2, 10])
            ->and($this->inputReader->readIntegerPair())->toBe([123456789, 2])
            ->and($this->inputReader->readIntegerPair())->toBe([-123456789, 987654321])
            ->and($this->inputReader->readIntegerPair())->toBe([0, 0])
            ->and($this->inputReader->readIntegerPair())->toBe([-1, 17])
            ->and($this->inputReader->readIntegerPair())->toBe([17, 0]);
    });

    it('throws an RuntimeException when there\'s nothing in the input stream', function () {
        fwrite($this->inputStream, '');
        rewind($this->inputStream);
        $this->inputReader->readIntegerPair();
    })->throws(RuntimeException::class);

    it('throws an UnexpectedValueException when the input stream has something other than a single integer', function (
        string $inputStreamContents
    ) {
        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);
        $this->inputReader->readIntegerPair();
    })
        ->with([
            'whitespace only' => [' '],
            'non-numeric string only ' => ['aabbcc'],
            'valid-invalid pair' => ['5 aabbcc'],
            'invalid-valid pair' => ['aabbcc 5'],
            'pair has a float' => ['5 12.85'],
            'pair has a float the other way round' => ['12.85 5'],
            'no separator' => ['7-6'],
            'semicolon separator' => ['7;6'],
            'comma separator' => ['7,6'],
        ])
        ->throws(UnexpectedValueException::class);
});

describe('readArrayOfIntegers', function () {
    it('reads a pair of integers from the input stream', function (
        string $inputStreamContents,
        int $arraySize,
        array $expectedArray
    ) {
        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);

        $actualArray = $this->inputReader->readArrayOfIntegers($arraySize);
        expect($actualArray)->toBe($expectedArray);
    })->with([
        'expected empty array' => ['', 0, []],
        'single element' => ['5', 1, [5]],
        'single lement with whitespace' => [' 5 ', 1, [5]],
        'multiple elements' => ["5\n8\n1", 3, [5, 8, 1]],
        'multiple negative elements' => ["-5\n-8\n-1", 3, [-5, -8, -1]],
        'lots of whitespace' => ["    -5    \n    -8   \n   -1     ", 3, [-5, -8, -1]],
        'mixed larger numbers' => ["6482048 \n -123456789 \n 6482048", 3, [6482048, -123456789, 6482048]],
    ]);

    it('throws an RuntimeException when there\'s nothing in the input stream', function () {
        fwrite($this->inputStream, '');
        rewind($this->inputStream);
        $this->inputReader->readArrayOfIntegers(5);
    })->throws(RuntimeException::class);

    it('throws when the stream has something other than a single integer on some line', function (
        string $inputStreamContents
    ) {
        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);
        $this->inputReader->readArrayOfIntegers(5);
    })
        ->with([
            'whitespace only' => ["\n\n\n\n\n"],
            'strings only' => ["aaa\nbbb\nccc\nddd\neee"],
            'string at the end' => ["1 \n 2\n 3 \n 4 \n eee"],
            'string at the beginning' => ["aaa \n 2\n 3 \n 4 \n 5"],
            'string in the middle' => ["1 \n 2\n ccc \n 4 \n 5"],
            'includes floats' => ["1 \n 2 \n 3 \n 4 \n 5.85"],
            'includes commas' => ["1 \n 2 \n 3 \n 4 \n 5,85"],
            'includes two integers on same line' => ["1 \n 2 \n 3 \n 4 \n 5 85"],
        ])
        ->throws(UnexpectedValueException::class);
});

describe('complex scenario', function () {
    it('reads mixed values from the input stream', function () {
        $inputStreamContents = <<<'INPUT'
1
5 3
1
2
4
8
9
      -181       24878785783
10
 9
  8
   7
    6
INPUT;

        fwrite($this->inputStream, $inputStreamContents);
        rewind($this->inputStream);

        expect($this->inputReader->readSingleInteger())->toBe(1)
            ->and($this->inputReader->readIntegerPair())->toBe([5, 3])
            ->and($this->inputReader->readArrayOfIntegers(3))->toBe([1, 2, 4])
            ->and($this->inputReader->readSingleInteger())->toBe(8)
            ->and($this->inputReader->readSingleInteger())->toBe(9)
            ->and($this->inputReader->readArrayOfIntegers(0))->toBe([])
            ->and($this->inputReader->readIntegerPair())->toBe([-181, 24878785783])
            ->and($this->inputReader->readArrayOfIntegers(5))->toBe([10, 9, 8, 7, 6]);
    });
});
