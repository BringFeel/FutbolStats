<?php
class Player {
    public int $id;
    public string $name;
    public string $position;
    public int $number;

    public function __construct($id, $name, $position, $number) {
        $this->id = $id;
        $this->name = $name;
        $this->position = $position;
        $this->number = $number;
    }
}