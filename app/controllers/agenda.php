<?php


require_once 'app/models/reunionModel.php';

$reunions = getAllReunions();

require_once 'app/views/agenda.php';