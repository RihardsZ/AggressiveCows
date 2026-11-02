<?php

declare(strict_types=1);

namespace AggressiveCows\Input;

use RuntimeException;
use UnexpectedValueException;

class InputReader
{
    /**
     * @param resource $inputStream
     */
    public function __construct(
        private $inputStream
    ) {
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
