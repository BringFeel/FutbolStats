<?php
class MatchGame {
    public $planning = [];
    public $execution = [];
    public $analysis = [];

    public function __construct() {
        if (!isset($_SESSION['matchData'])) {
            $_SESSION['matchData'] = [
                'planning' => [],
                'execution' => [],
                'analysis' => []
            ];
        }
        $this->planning = &$_SESSION['matchData']['planning'];
        $this->execution = &$_SESSION['matchData']['execution'];
        $this->analysis = &$_SESSION['matchData']['analysis'];
    }

    public function addData($phase, $data) {
        if (in_array($phase, ['planning', 'execution', 'analysis'])) {
            $_SESSION['matchData'][$phase][] = $data;
        }
    }

    public function getData($phase) {
        return $_SESSION['matchData'][$phase] ?? [];
    }
}