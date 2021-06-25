<?php
class ExampleComponent
{
    /** @var ExampleComponent */
    public static $instance;

    function __construct()
    {

    }
}

// INIT COMPONENT
ExampleComponent::$instance = new ExampleComponent();