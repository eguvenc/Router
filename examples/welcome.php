<?php


$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome', 'Welcome/index');
$router->map('GET', 'welcome/index/(\d+)', 'Welcome/index/$1');