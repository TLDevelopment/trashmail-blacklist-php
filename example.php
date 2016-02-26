<?php
include_once('./TrashmailBlacklist.php');

$b = new TrashmailBlacklist();

//$b->setCaching(TrashmailBlacklistCache::CACHE_TYPE_DIR); coming soon

if($b->isBlacklisted('spambog.com')) {
    echo "spambog.com ist blacklisted\r\n";
} else {
    echo "spambog.com ist nicht blacklisted\r\n";
}

if($b->isBlacklisted('test.de')) {
    echo "test.de ist blacklisted\r\n";
} else {
    echo "test.de ist nicht blacklisted\r\n";
}
?>