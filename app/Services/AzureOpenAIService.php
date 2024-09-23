<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AzureOpenAIService
{
    private Client $client;

    private string $deployment;

    private string $apiVersion;

    private array $functions;

    public function __construct()
    {
        $config = Config::get('services.azure-openai');

        $this->client = new Client([
            'base_uri' => $config['endpoint'],
            'headers' => [
                'api-key' => $config['api_key'],
                'Content-Type' => 'application/json',
            ],
        ]);
        $this->deployment = $config['deployment'];
        $this->apiVersion = $config['api_version'];
        $this->functions = $this->getFunctionDefinitions();
    }

    public function processEventInput(string $type, string $title, ?string $description = null): array
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a GitHub event expert and can provide better descriptions for GitHub events.'],
            ['role' => 'user', 'content' => "The event type is {$type}. The title is: {$title}. ".($description ? "Description: {$description}" : '')],
        ];

        try {
            // Send a request to the AI, including function definitions
            $response = $this->chatCompletion($messages, $this->functions);

            // Log the full response for debugging
            Log::info('Azure OpenAI Response', ['response' => $response]);

            // Check if a function call is being returned
            if (isset($response['choices'][0]['message']['function_call'])) {
                return $this->handleFunctionCall($response);
            }

            // Return the AI response in minimal format
            return $this->extractSolidJson($response);
        } catch (Exception $e) {
            Log::error('Azure OpenAI Service Error', ['error' => $e->getMessage()]);

            return [
                'title' => 'Error occurred',
            ];
        }
    }

    private function chatCompletion(array $messages, ?array $functions = null, array $options = []): array
    {
        $data = $this->prepareChatCompletionData($messages, $functions, $options);

        try {
            $response = $this->client->post("openai/deployments/{$this->deployment}/chat/completions", [
                'query' => ['api-version' => $this->apiVersion],
                'json' => $data,
            ]);

            $body = $response->getBody()->getContents();

            return json_decode($body, true);
        } catch (GuzzleException $e) {
            Log::error('Azure OpenAI API Error', ['error' => $e->getMessage(), 'data' => $data]);
            throw new Exception('Azure OpenAI API error: '.$e->getMessage());
        }
    }

    private function prepareChatCompletionData(array $messages, ?array $functions, array $options): array
    {
        $data = [
            'messages' => $messages,
            'max_tokens' => $options['max_tokens'] ?? Config::get('ai.max_tokens', 150),
            'temperature' => $options['temperature'] ?? Config::get('ai.temperature', 0.7),
        ];

        // Add functions only if they are defined and function_call is enabled
        if (! empty($functions)) {
            $data['functions'] = $functions;
            $data['function_call'] = $options['function_call'] ?? 'auto';
        }

        return $data;
    }

    private function handleFunctionCall(array $response): array
    {
        $functionCall = $response['choices'][0]['message']['function_call'];

        // Extract the function name and arguments (default to an empty array if null)
        $functionName = $functionCall['name'];
        $arguments = json_decode($functionCall['arguments'], true) ?? [];

        // Execute the function and get the result
        $functionResult = $this->executeFunction($functionName, $arguments);

        // Return the result of the function call in the format you want
        return [
            'title' => $functionResult['message'] ?? 'No content received after function execution.',
        ];
    }

    private function executeFunction(string $functionName, array $arguments): array
    {
        // Handle the function calls here
        if ($functionName === 'enhanceGitHubEvent') {
            return $this->enhanceGitHubEvent($arguments);
        }

        Log::warning("Attempted to call undefined function: {$functionName}");

        return ['status' => 'error', 'message' => "Function {$functionName} not found"];
    }

    private function enhanceGitHubEvent(array $arguments): array
    {
        $type = $arguments['type'] ?? 'Unknown';
        $title = $arguments['title'] ?? 'No title provided';
        $description = $arguments['description'] ?? '';

        // Make the AI generate a more descriptive message based on the event type and title
        $messages = [
            ['role' => 'system', 'content' => 'You are an expert in GitHub event analysis. Provide a more descriptive commit message or event description based on the event type and title.'],
            ['role' => 'user', 'content' => "Event Type: {$type}. Title: {$title}. ".($description ? "Description: {$description}" : '')],
        ];

        try {
            // Call AI to generate a dynamic response
            $response = $this->chatCompletion($messages);

            // Extract the AI-generated content
            $aiResponse = $response['choices'][0]['message']['content'] ?? 'No content received from AI';

            return [
                'status' => 'success',
                'message' => $aiResponse,
            ];
        } catch (Exception $e) {
            Log::error('Error generating AI response', ['error' => $e->getMessage()]);

            return [
                'status' => 'error',
                'message' => 'Failed to generate AI response.',
            ];
        }
    }

    private function getFunctionDefinitions(): array
    {
        return [
            [
                'name' => 'enhanceGitHubEvent',
                'description' => 'Enhance GitHub event data and provide a better message.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'type' => [
                            'type' => 'string',
                            'description' => 'The type of the GitHub event',
                        ],
                        'title' => [
                            'type' => 'string',
                            'description' => 'The title or summary of the GitHub event',
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'Optional description of the GitHub event',
                        ],
                    ],
                    'required' => ['type', 'title'],
                ],
            ],
        ];
    }

    private function extractSolidJson(array $response): array
    {
        // Check if the response has choices and a message with content
        if (isset($response['choices'][0]['message']['content']) && ! empty($response['choices'][0]['message']['content'])) {
            // Extract the AI response
            $aiResponse = trim($response['choices'][0]['message']['content']);

            return [
                'title' => $aiResponse,
            ];
        } else {
            // Log a warning if the AI response is empty
            Log::warning('No content received in AI response', ['response' => $response]);

            return [
                'title' => 'No content received.',
            ];
        }
    }
}
