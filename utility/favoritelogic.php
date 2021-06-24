<?php
if(isset($_POST['addFavorite'])){
    //var_dump($_POST['addFavorite']);
    FavoriteService::$instance->insertFavorite($_POST['addFavorite'], $_SESSION['UserID']);
}

if(isset($_POST['removeFavorite'])){
    //var_dump($_POST['removeFavorite']);
    FavoriteService::$instance->removeFavorite($_POST['removeFavorite'], $_SESSION['UserID']);
}


?>