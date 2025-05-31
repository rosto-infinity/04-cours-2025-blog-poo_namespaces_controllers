<?php
use Libraries\Controllers\Comment;
require_once "libraries/Controllers/Comment.php";

$controllerComment= new Comment();
$controllerComment->delete();
