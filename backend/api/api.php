<?php

require_once __DIR__ . '/../conf/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$container = smartMatchBootstrap();
$method = Request::method();
$resource = strtolower(trim((string) ($_GET['resource'] ?? '')));
$id = isset($_GET['id']) && $_GET['id'] !== '' ? (int) $_GET['id'] : null;

if ($method === 'PUT') {
    Request::parseMultipartPut();
}

function apiRequireAdmin(AuthController $authController): void
{
    if (!$authController->isAdmin()) {
        Response::send(Response::errorPayload(['Non autorisé'], 401));
    }
}

function apiExceptionErrors(Throwable $exception): array
{
    $messages = preg_split('/\r\n|\r|\n/', $exception->getMessage());
    return array_values(array_filter($messages, static fn($message): bool => $message !== ''));
}

try {
    $body = Request::body();
    $files = Request::files();
    $authController = $container['authController'];

    switch ($resource) {
        case 'login':
            $result = $authController->login($method, $body);
            break;
        case 'logout':
            $result = $authController->logout($method);
            break;
        case 'session':
            $result = $authController->session($method);
            break;
        case 'users':
            apiRequireAdmin($authController);
            $result = $authController->users($method);
            break;
        case 'offres':
            if ($method !== 'GET') {
                apiRequireAdmin($authController);
            }
            $result = $container['offreController']->handle($method, $id, $body, $files);
            break;
        case 'competences':
            apiRequireAdmin($authController);
            $result = $container['competenceController']->handle($method, $id, $body);
            break;
        default:
            Response::send(Response::errorPayload(['Ressource inconnue'], 404));
    }

    Response::send(Response::successPayload($result['data'], $result['status']));
} catch (UnauthorizedException $exception) {
    Response::send(Response::errorPayload([$exception->getMessage()], 401));
} catch (ForbiddenException $exception) {
    Response::send(Response::errorPayload([$exception->getMessage()], 403));
} catch (MethodNotAllowedException $exception) {
    Response::send(Response::errorPayload([$exception->getMessage()], 405));
} catch (OutOfBoundsException $exception) {
    Response::send(Response::errorPayload([$exception->getMessage()], 404));
} catch (InvalidArgumentException $exception) {
    Response::send(Response::errorPayload(apiExceptionErrors($exception), 422));
} catch (Throwable $exception) {
    Response::send(Response::errorPayload(['Erreur serveur : ' . $exception->getMessage()], 500));
}
