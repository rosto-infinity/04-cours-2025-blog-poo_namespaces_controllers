<?php
namespace Libraries;

class Http
{
   public static function redirect(string $path)
  {
    header("Location: $path");
    exit(); // Terminer le script
  }
}
