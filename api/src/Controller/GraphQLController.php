<?php

declare(strict_types=1);

namespace App\Controller;

use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use JsonException;
use Throwable;

final class GraphQLController
{
    public function __construct(private readonly Schema $schema)
    {
    }

    public function handle(): string
    {
        header('Content-Type: application/json; charset=UTF-8');

        try {
            $input = $this->readInput();
            $query = $input['query'] ?? null;

            if (!is_string($query) || $query === '') {
                http_response_code(400);
                return $this->encode(['error' => ['message' => 'GraphQL query is required']]);
            }

            $variables = is_array($input['variables'] ?? null) ? $input['variables'] : null;
            $operationName = is_string($input['operationName'] ?? null) ? $input['operationName'] : null;

            $executionResult = GraphQL::executeQuery(
                $this->schema,
                $query,
                null,
                null,
                $variables,
                $operationName
            );

            return $this->encode($executionResult->toArray($this->debugFlags()));
        } catch (Throwable $throwable) {
            http_response_code(500);
            return $this->encode([
                'error' => [
                    'message' => $this->isDebug() ? $throwable->getMessage() : 'Internal server error',
                ],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function readInput(): array
    {
        $rawInput = file_get_contents('php://input');
        if ($rawInput === false || trim($rawInput) === '') {
            return [];
        }

        try {
            $decoded = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function encode(array $payload): string
    {
        try {
            return json_encode($payload, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return '{"error":{"message":"JSON encoding failed"}}';
        }
    }

    private function debugFlags(): int
    {
        return $this->isDebug() ? DebugFlag::INCLUDE_DEBUG_MESSAGE : DebugFlag::NONE;
    }

    private function isDebug(): bool
    {
        return ($_ENV['APP_DEBUG'] ?? '0') === '1';
    }
}
