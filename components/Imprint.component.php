<?php
class ImprintComponent
{
    /** @var ImprintComponent */
    public static $instance;
    function __construct()
    {
        if(isset($_GET["action"]) && $_GET["action"] == "imprint" ){
            require_once "./pages/impressum.html";
        }
    }
}

// INIT COMPONENT
ImprintComponent::$instance = new ImprintComponent();