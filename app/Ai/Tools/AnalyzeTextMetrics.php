<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class AnalyzeTextMetrics implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'This tool analyzes a text and returns readability statistics including word count, character count, sentence count, reading/speaking time, and Flesch Reading Ease score.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $text = $request['text'] ?? '';

        if (empty(trim($text))) {
            return 'The provided text is empty.';
        }

        $cleanText = trim($text);

        // Count words
        preg_match_all('/\p{L}+/u', $cleanText, $wordMatches);
        $words = $wordMatches[0];
        $wordCount = count($words);

        // Count characters
        $charCount = mb_strlen($cleanText);

        // Count sentences
        $sentences = preg_split('/[.!?]+(?:\s|$)/u', $cleanText, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = max(1, count($sentences));

        // Count syllables
        $syllableCount = 0;
        foreach ($words as $word) {
            $syllableCount += $this->countSyllables($word);
        }

        // Flesch Reading Ease
        if ($wordCount > 0) {
            $fleschScore = 206.835 - 1.015 * ($wordCount / $sentenceCount) - 84.6 * ($syllableCount / $wordCount);
            $fleschScore = round($fleschScore, 1);

            if ($fleschScore >= 90) {
                $interpretation = 'Very Easy (5th grade level)';
            } elseif ($fleschScore >= 80) {
                $interpretation = 'Easy (6th grade level)';
            } elseif ($fleschScore >= 70) {
                $interpretation = 'Fairly Easy (7th grade level)';
            } elseif ($fleschScore >= 60) {
                $interpretation = 'Standard (8th-9th grade level)';
            } elseif ($fleschScore >= 50) {
                $interpretation = 'Fairly Difficult (10th-12th grade level)';
            } elseif ($fleschScore >= 30) {
                $interpretation = 'Difficult (College level)';
            } else {
                $interpretation = 'Very Difficult (College graduate level)';
            }
        } else {
            $fleschScore = 100;
            $interpretation = 'N/A';
        }

        // Reading / Speaking times
        $readingTimeSec = round(($wordCount / 200) * 60);
        $speakingTimeSec = round(($wordCount / 130) * 60);

        $readingTime = $readingTimeSec < 60 ? "{$readingTimeSec} sec" : round($readingTimeSec / 60, 1).' min';
        $speakingTime = $speakingTimeSec < 60 ? "{$speakingTimeSec} sec" : round($speakingTimeSec / 60, 1).' min';

        return json_encode([
            'word_count' => $wordCount,
            'character_count' => $charCount,
            'sentence_count' => $sentenceCount,
            'syllable_count' => $syllableCount,
            'reading_time' => $readingTime,
            'speaking_time' => $speakingTime,
            'flesch_reading_ease' => $fleschScore,
            'readability_level' => $interpretation,
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Syllable counting helper.
     */
    private function countSyllables(string $word): int
    {
        $word = strtolower(trim($word));
        if (strlen($word) <= 3) {
            return 1;
        }
        $word = preg_replace('/(?:es|ed|e)$/u', '', $word);
        $word = preg_replace('/^y/u', '', $word);
        preg_match_all('/[aeiouy]{1,2}/u', $word, $matches);
        return max(1, count($matches[0]));
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'text' => $schema->string()->required(),
        ];
    }
}
