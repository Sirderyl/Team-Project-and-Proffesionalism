<?php

/**
 * Utility class for argument parsing
 * @author Kieran
 */
class Arguments
{
    private function __construct()
    {
    }

    /**
     * Parse a string as a JSON object or array
     * @param string $json
     * @return array<string|int, mixed>
     * @throws InvalidArgumentException if the string is not a JSON object or array
     */
    public static function parseJson(string $json): array
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON');
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException('JSON is not an object or array');
        }
        return $data;
    }

    /**
     * Get a string from an array
     * @param array<string|int, mixed> $arr
     */
    public static function getString(array $arr, string|int $key): string
    {
        $value = $arr[$key] ?? null;
        if (!is_string($value)) {
            throw new InvalidArgumentException("Expected $key to be a string");
        }
        return $value;
    }
}
