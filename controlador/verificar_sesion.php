<?php

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?c=login&m=login");
    exit;
}

?>