<?php
Phar::mapPhar('a.phar');
include 'phar://a.phar/' . (PHP_SAPI == 'cli' ? 'cli' : 'web') . '.php';
__HALT_COMPILER();
