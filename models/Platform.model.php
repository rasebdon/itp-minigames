<?php
class Platform {
    public $id;
    public $name;

    private function __construct($name, $id) {
        $this->name = $name;
        $this->id = $id;
    }

    public static function Windows() {
        return new Platform("win", 1);
    }
    public static function Linux() {
        return new Platform("lin", 2);
    }
    public static function Mac() {
        return new Platform("mac", 3);
    }
}