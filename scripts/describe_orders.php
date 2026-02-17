<?php
$m = new mysqli('localhost','root','','fooddash_db');
if (!$m) {
    echo "connect failed\n";
    exit(1);
}
$res = $m->query('DESCRIBE orders');
if (! $res) {
    echo "DESCRIBE failed: " . $m->error . "\n";
    exit(1);
}
while ($r = $res->fetch_assoc()) {
    print_r($r);
}
