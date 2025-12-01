<?php
require_once "src/model/Barang.php";
require_once "src/repository/ItemRepository.php";
require_once "src/view/ItemView.php";
require_once "src/view/MenuView.php";
require_once "src/controller/ItemController.php";

$repo = new ItemRepository();
$view = new ItemView();
$menu = new MenuView();
$controller = new ItemController($repo, $view, $menu);

$controller->run();
?>