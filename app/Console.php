<?php
echo "Starting Sw0rdfish interactive console...\n\n";

# Nothing fancy here, really,  this will just basically require the index
# therefore bootstraping the whole application so we can reference all of our
# objects, methods, etc. in the interactive shell
require(__DIR__ . '/../public/index.php');
