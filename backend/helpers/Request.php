<?php

class Request
{
    public static function method(): string
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? '';
        return $override !== '' ? strtoupper((string) $override) : $method;
    }

    public static function body(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        if ($contentType === '' && function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                if (strcasecmp((string) $name, 'Content-Type') === 0) {
                    $contentType = (string) $value;
                    break;
                }

            }
        }

        if (stripos($contentType, 'application/json') !== false) {
            return self::decode(file_get_contents('php://input') ?: '');
        }
        if (!empty($_POST)) {
            return $_POST;
        }

        return self::decode(file_get_contents('php://input') ?: '');
    }

    public static function parseMultipartPut(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        if (stripos($contentType, 'multipart/form-data') === false
            || !preg_match('/boundary=(?:"([^"]+)"|([^;]+))/i', $contentType, $matches)) {
            return;
        }

        $boundary = $matches[1] !== '' ? $matches[1] : trim($matches[2]);
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return;
        }

        foreach (explode('--' . $boundary, $raw) as $part) {
            $part = ltrim($part, "\r\n");
            if ($part === '' || $part === '--' || strpos($part, '--') === 0) {
                continue;
            }
            $separator = strpos($part, "\r\n\r\n");
            if ($separator === false || !preg_match('/name="([^"]*)"/i', substr($part, 0, $separator), $nameMatch)) {
                continue;
            }

            $headers = substr($part, 0, $separator);
            $body = (string) preg_replace("/\r\n$/", '', substr($part, $separator + 4));
            $name = $nameMatch[1];
            if (preg_match('/filename="([^"]*)"/i', $headers, $fileMatch)) {
                if ($fileMatch[1] === '') {
                    continue;
                }
                preg_match('/Content-Type:\s*([^\r\n]+)/i', $headers, $typeMatch);
                $tmp = tempnam(sys_get_temp_dir(), 'smatch_');
                if ($tmp === false || file_put_contents($tmp, $body) === false) {
                    throw new RuntimeException('Impossible de préparer le fichier téléchargé.');
                }
                $_FILES[$name] = [
                    'name' => $fileMatch[1],
                    'type' => isset($typeMatch[1]) ? trim($typeMatch[1]) : 'application/octet-stream',
                    'tmp_name' => $tmp,
                    'error' => UPLOAD_ERR_OK,
                    'size' => strlen($body),
                ];
            } else {
                $_POST[$name] = $body;
            }
        }
    }

    public static function files(): array
    {
        return $_FILES;
    }

    private static function decode(string $raw): array
    {
        if ($raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}

class MethodNotAllowedException extends RuntimeException {}
