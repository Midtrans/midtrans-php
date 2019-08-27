<?php
  $base = $_SERVER['REQUEST_URI'];
?>

<h3>Selected Items:</h3>
<ul>
    <li>Jeruk 2 kg x @20000</li>
    <li>Apel 3 kg x @18000</li>
</ul>

<h4>Total: Rp 94.000,00</h4>

<form action="<?php echo $base ?>checkout-process.php" method="POST">
    <input type="hidden" name="amount" value="94000"/>
    <input type="submit" value="Confirm">
</form>
