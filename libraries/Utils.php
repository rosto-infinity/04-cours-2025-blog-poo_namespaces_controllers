<?php

namespace Libraries;

class Utils
{
  public static function cleanInput($data)
  {
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
  }

  public static function createSlug($title)
  {
     $title = self::removeAccents($title);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title); // Alternative plus robuste
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($slug));
        return trim($slug, '-');
  }

  public static function removeAccents($string)
  {
    $accents = [
      'à' => 'a',
      'á' => 'a',
      'â' => 'a',
      'ã' => 'a',
      'ä' => 'a',
      'å' => 'a',
      'ç' => 'c',
      'è' => 'e',
      'é' => 'e',
      'ê' => 'e',
      'ë' => 'e',
      'ì' => 'i',
      'í' => 'i',
      'î' => 'i',
      'ï' => 'i',
      'ñ' => 'n',
      'ò' => 'o',
      'ó' => 'o',
      'ô' => 'o',
      'õ' => 'o',
      'ö' => 'o',
      'ø' => 'o',
      'ù' => 'u',
      'ú' => 'u',
      'û' => 'u',
      'ü' => 'u',
      'ý' => 'y',
      'ÿ' => 'y',
      'À' => 'A',
      'Á' => 'A',
      'Â' => 'A',
      'Ã' => 'A',
      'Ä' => 'A',
      'Å' => 'A',
      'Ç' => 'C',
      'È' => 'E',
      'É' => 'E',
      'Ê' => 'E',
      'Ë' => 'E',
      'Ì' => 'I',
      'Í' => 'I',
      'Î' => 'I',
      'Ï' => 'I',
      'Ñ' => 'N',
      'Ò' => 'O',
      'Ó' => 'O',
      'Ô' => 'O',
      'Õ' => 'O',
      'Ö' => 'O',
      'Ø' => 'O',
      'Ù' => 'U',
      'Ú' => 'U',
      'Û' => 'U',
      'Ü' => 'U',
      'Ý' => 'Y'
    ];
    return strtr($string, $accents);
  }
 
}
