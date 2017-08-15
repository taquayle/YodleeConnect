<?php

echo "GD: ", extension_loaded('gd2') ? 'OK' : 'MISSING', '<br>';
echo "XML: ", extension_loaded('xml') ? 'OK' : 'MISSING', '<br>';
echo "zip: ", extension_loaded('zip') ? 'OK' : 'MISSING', '<br>';

echo phpinfo();
?>
