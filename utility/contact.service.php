<?php
if(isset($_GET["action"]) && $_GET["action"] == "contact"){
    require_once "./pages/contact.html";
    if(isset($_POST["send"])){
        if($_POST["request"] != ""){
            if(!isset($_SESSION['UserID'])){
                echo "You must be loggedin to send a Request!";
            }else{
                ContactService::$instance->addTicket($_POST["request"], $_SESSION['UserID'], $_POST["subject"]);
                echo "Request sucessfully sent!";
            }
        }else{
            echo "Request can't be empty";
        }
    }
}