<?php
if(isset($_POST['addFavorite'])){
    //var_dump($_POST['addFavorite']);
    FavoriteService::$instance->insertFavorite($_GET['id'], $_POST['addFavorite']);
}

if(isset($_POST['removeFavorite'])){
    //var_dump($_POST['removeFavorite']);
    FavoriteService::$instance->removeFavorite($_GET['id'], $_POST['removeFavorite']);
}


?>