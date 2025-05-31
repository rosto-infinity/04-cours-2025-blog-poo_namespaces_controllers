<?php

use Libraries\Http;
session_start();
require_once 'libraries/Renderer.php'; 
require_once 'libraries/Http.php'; 
require_once 'libraries/Utils.php'; 


session_unset(); // -Détruire toutes les variables de session
session_destroy(); // Détruire la session

Http::redirect('index.php'); // Rediriger vers la page de connexion



