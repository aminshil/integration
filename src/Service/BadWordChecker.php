<?php

namespace App\Service;

class BadWordChecker
{
    private $badWords;

    public function __construct(array $badWords)
    {
        $this->badWords = $badWords;
    }

    public function containsBadWords(string $input): bool
    {
        $input = strtolower($input); // Convert input to lowercase for case-insensitive comparison

        foreach ($this->badWords as $badWord) {
            if (strpos($input, $badWord) !== false) {
                return true; // Input contains a bad word
            }
        }

        return false; // No bad words found in input
    }
}
