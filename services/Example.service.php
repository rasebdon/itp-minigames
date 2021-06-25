<?php
class ExampleService
{
    /** @var ExampleService */
    public static $instance;

    function __construct()
    {

    }
}

// INIT SERVICE
ExampleService::$instance = new ExampleService();

// Inject dependencies via the constructor
// For example for a database dependency
// ExampleService::$instance = new ExampleService(Database::$instance);