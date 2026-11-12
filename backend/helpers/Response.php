<?php

class Response
{
    public static function successPayload($data, int $status = 200): array
    {
        return ['status' => $status, 'payload' => ['success' => true, 'data' => $data]];
    }

    public static function errorPayload(array $errors, int $status = 400): array
    {
        return ['status' => $status, 'payload' => ['success' => false, 'errors' => array_values($errors)]];
    }

    public static function send(array $result): never
    {
        http_response_code($result['status']);
        echo json_encode($result['payload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
