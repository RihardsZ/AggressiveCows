<?php

declare(strict_types=1);

namespace AggressiveCows\Solver;

class BinarySearcher
{
    /**
     * NB! This assumes that the bounds are both non-negative and the upper bound is not less than the lower one,
     * but the assumption is not checked here. This could be modified to return nulls when nothing passes the predicate.
     * This also assumes that the $testPredicate is monotone, i.e.
     * if it returns true for some x, then it will always return true for any y > x
     * @param callable(int): bool $testPredicate
     */
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
