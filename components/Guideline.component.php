<?php
class GuidelineComponent
{
    /** @var GuidelineComponent */
    public static $instance;
    function __construct()
    {
        if(isset($_GET["action"]) && $_GET["action"] == "guide" ){
            require_once "./pages/guidelines.html";
        }
    }
}

// INIT COMPONENT
GuidelineComponent::$instance = new GuidelineComponent();