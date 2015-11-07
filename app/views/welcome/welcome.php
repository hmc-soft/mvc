<?php

use HMC\Config;
use HMC\Language;

?>

<div class="page-header">
	<h1><?php echo $data['title'] ?></h1>
</div>

<p><?php echo $data['welcome_message'] ?></p>

<a class="btn btn-md btn-success" href="<?php echo Config::SITE_URL();?>/subpage">
	<?php echo Language::tr('open_subpage'); ?>
</a>
