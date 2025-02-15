<?php
switch (request()->getHttpHost()) {
    case env('HOST_SCHOOL'):
        require 'web_school.php';
        break;
    case env('HOST_HOOKS'):
        require 'web_hooks.php';
        break;
}
