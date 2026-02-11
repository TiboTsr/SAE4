<?php

require_once 'app/models/gradeModel.php';
require_once 'app/models/files_save.php';

$products = getGrade();

require_once 'app/views/grade.php';