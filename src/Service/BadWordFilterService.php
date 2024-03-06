<?php
namespace App\Service;

class BadWordFilterService
{
    private $badWords = ['idiot', 'stupid', 'cunt' , 'retard']; // Add your list of bad words here

    public function filter(string $text): string
    {
        // Convert text to lowercase for case-insensitive comparison
        $lowercaseText = strtolower($text);

        // Replace each occurrence of bad words with asterisks (*) of the same length
        foreach ($this->badWords as $badWord) {
            $text = preg_replace("/$badWord/ui", str_repeat('*', mb_strlen($badWord)), $text);
        }

        return $text;
    }
}
