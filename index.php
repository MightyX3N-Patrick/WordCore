<?php
define('WC_ROOT', __DIR__);
define('WC_VERSION', '1.0.0');

require_once WC_ROOT . '/core/bootstrap.php';

WordCore::init();
WordCore::route();
