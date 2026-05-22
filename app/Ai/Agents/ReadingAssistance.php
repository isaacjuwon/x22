<?php

namespace App\Ai\Agents;

use App\Ai\Tools\ExtractHeaders;
use App\Ai\Tools\ExtractKeywords;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class ReadingAssistance implements Agent, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a professional reading assistant. You help the user summarize, extract key info, organize, and analyze text. Use the provided tools (ExtractKeywords, ExtractHeaders) to automatically extract key concepts or outline the text when asked.';
    }

    /**
     * Get the tools available to the agent.
     *
     * @return iterable<\Laravel\Ai\Contracts\Tool>
     */
    public function tools(): iterable
    {
        return [
            new ExtractKeywords,
            new ExtractHeaders,
        ];
    }
}
