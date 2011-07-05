<?php

global $data;

?>
<h1>Our Spicy Taco Promotions</h1>
<ul>
<?php foreach($data['promotions'] as $buy_amount => $free_amount) { ?>
	<li>Buy <?php echo $buy_amount; ?>, Get <?php echo $free_amount; ?> FREE!</li>
<?php } ?>
</ul>