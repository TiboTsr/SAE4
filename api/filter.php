<?php

// Le filter permets de vérifier que les types récupérés sont corrects
// On va aussi les nettoyer pour éviter les injections XSS. Les injections SQL sont gérées par la classe DB
class Filter
{
    private static function deny(mixed $value, string $attribute)
    {
        http_response_code(400);
        echo json_encode(["message" => $value . " is not a valid " . $attribute . " in this context"]);
        exit();
    }

    public static function string(mixed $value, int $minLenght = 0, int $maxLenght = 5000): string
    {
        if (!is_scalar($value)) {
            self::deny($value, "string");
        }

        $filtered = (string)$value;
        $length = function_exists('mb_strlen')
            ? mb_strlen($filtered, 'UTF-8')
            : strlen($filtered);

        if ($length < $minLenght || $length > $maxLenght) {
            self::deny($value, "string");
        }

        return $filtered;
    }

    public static function email(mixed $value, int $minLenght = 5, int $maxLenght = 254): string | bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($filtered === false || strlen($filtered) < $minLenght || strlen($filtered) > $maxLenght) {
            self::deny($value, "email");
        }

        return $filtered;
    }

    public static function int(mixed $value, int $min = 0, int $max = PHP_INT_MAX): int | bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT);

        if ($filtered === false || $filtered < $min || $filtered > $max) {
            self::deny($value, "int");
        }

        return $filtered;
    }

    public static function float(mixed $value, int $min = 0, int $max = PHP_INT_MAX): float | bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);

        if ($filtered === false || $filtered < $min || $filtered > $max) {
            self::deny($value, "float");
        }

        return $filtered;
    }

    public static function bool(mixed $value): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($filtered === null) {
            self::deny($value, "bool");
        }

        return $filtered;
    }

    public static function date(mixed $value): string
    {
        if (!is_string($value)) {
            self::deny($value, "date");
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();

        if (
            $date === false ||
            ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) ||
            $date->format('Y-m-d') !== $value
        ) {
            self::deny($value, "date");
        }

        return $value;
    }

    public static function json(mixed $value): array
    {
        if (!is_array($value)) {
            self::deny($value, "json");
        }

        return $value;
    }
}
