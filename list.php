<?php
echo "<pre>";
echo "Current directory:\n";
echo __DIR__ . "\n\n";

echo "Folders & files here:\n";
print_r(scandir(__DIR__));

echo "\n\nParent directory:\n";
print_r(scandir(dirname(__DIR__)));
echo "</pre>";
