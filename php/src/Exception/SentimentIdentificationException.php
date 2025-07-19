<?php

namespace App\Exception;

use Throwable;

class SentimentIdentificationException extends \RuntimeException
{
    private const DEFAULT_MESSAGE = 'Не удалось получить ответ от сервера или однозначно идентифицировать ответ от сервера';
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = $message?: static::DEFAULT_MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
