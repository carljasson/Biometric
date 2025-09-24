<?php
$link = mysqli_connect(
    'mysql.hostinger.com',  // or whatever DB_HOST should be
    'u802714156_biomedAccessP',
    '1MedPass2025',
    'u802714156_biomedAccess'
);
if(!$link) { die('Connect Error: '.mysqli_connect_error()); }
echo '✅ DB Connected!';
