<?php
    $base = $_SERVER['REQUEST_URI'];
?>

<form action="<?php echo $base ?>checkout-process.php" method="GET">
    <input type="submit" value="Pay with Snap Redirect">
</form>
