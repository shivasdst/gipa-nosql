<?php

define('BASE_URL', 'http://192.168.1.101/gipa-nosql/');
define('PUBLIC_URL', BASE_URL . 'public/');
define('DATA_URL', PUBLIC_URL . 'data/');
define('METADATA_URL', PUBLIC_URL . 'metaData/');
define('FOREIGN_KEYS_URL', PUBLIC_URL . 'foreignKeys/');
define('JSON_PRECAST_URL', BASE_URL . 'json-precast/');
define('FLAT_URL', BASE_URL . 'application/views/flat/');
define('STOCK_IMAGE_URL', PUBLIC_URL . 'images/stock/');

// Physical location of resources
define('PHY_BASE_URL', '/var/www/html/gipa-nosql/');
define('PHY_PUBLIC_URL', PHY_BASE_URL . 'public/');
define('PHY_DATA_URL', PHY_PUBLIC_URL . 'data/');
define('PHY_METADATA_URL', PHY_PUBLIC_URL . 'metaData/');
define('PHY_FOREIGN_KEYS_URL', PHY_PUBLIC_URL . 'foreignKeys/');
define('PHY_JSON_PRECAST_URL', PHY_BASE_URL . 'json-precast/');
define('PHY_FLAT_URL', PHY_BASE_URL . 'application/views/flat/');
define('PHY_STOCK_IMAGE_URL', PHY_PUBLIC_URL . 'images/stock/');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '27017');
define('DB_NAME', 'gipa');
define('DB_USER', 'gipaUSER');
define('DB_PASSWORD', 'gipa123');

// Git config
define('GIT_USER_NAME', 'sriranga-deploy');
define('GIT_PASSWORD', 'xxx');
define('GIT_REPO', 'github.com/SrirangaDigital/gipa-nosql.git');
define('GIT_REMOTE', 'https://' . GIT_USER_NAME . ':' . GIT_PASSWORD . '@' . GIT_REPO);
define('GIT_EMAIL', 'accounts@srirangadigital.com');

?>
