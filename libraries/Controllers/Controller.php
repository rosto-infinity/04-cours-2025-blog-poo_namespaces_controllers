<?php

namespace Libraries\Controllers;

use Libraries\Http;
use Libraries\Renderer;

require_once 'libraries/Renderer.php';
require_once 'libraries/Http.php';

abstract class Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Redirige vers une page donnée
     */
    protected function redirect(string $path)
    {
        Http::redirect($path);
    }

    /**
     * Rendu d'une vue avec variables
     */
    protected function render(string $view, array $variables = [])
    {
        Renderer::render($view, $variables);
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['auth']['id']);
    }

    /**
     * Récupère l'utilisateur connecté (ou null)
     */
    protected function getAuthUser()
    {
        return $_SESSION['auth'] ?? null;
    }
}
