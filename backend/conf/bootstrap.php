<?php

require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../classes/dao/OffreDAO.php';
require_once __DIR__ . '/../classes/dao/CompetenceDAO.php';
require_once __DIR__ . '/../classes/dao/UserDAO.php';
require_once __DIR__ . '/../classes/services/OffreService.php';
require_once __DIR__ . '/../classes/services/CompetenceService.php';
require_once __DIR__ . '/../classes/services/AuthService.php';
require_once __DIR__ . '/../controller/OffreController.php';
require_once __DIR__ . '/../controller/CompetenceController.php';
require_once __DIR__ . '/../controller/AuthController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function smartMatchBootstrap(): array
{
    $dataDirectory = __DIR__ . '/../data';
    $uploadsDirectory = $dataDirectory . '/uploads/offres';
    $offreDao = new OffreDAO($dataDirectory . '/offres.json');
    $competenceDao = new CompetenceDAO($dataDirectory . '/competences.json');
    $userDao = new UserDAO($dataDirectory . '/users.json');
    $authService = new AuthService($userDao);

    return [
        'offreController' => new OffreController(
            new OffreService($offreDao, $userDao, new ImageUpload($uploadsDirectory))
        ),
        'competenceController' => new CompetenceController(new CompetenceService($competenceDao)),
        'authController' => new AuthController($authService),
    ];
}
