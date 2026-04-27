<?php
session_start();
session_unset();
session_destroy();
header('Location: login.html?success=' . urlencode('You have been logged out.'));
exit;
