<?php
$input = preg_replace('/\s+/', '', $input);
$input = strip_tags($input);
$input = substr($input,0,3);
return $input;