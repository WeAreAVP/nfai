<?php
	$t_order = $this->getVar('t_order');
	$o_client_services_config = caGetClientServicesConfiguration();
?>
You were sent the following message by <em><?php print $this->getVar('sender_name'); ?></em> on <em><?php print date('F j, Y g:i a', $this->getVar('sent_on')); ?></em>:

<p>Your order <?php print $t_order->getOrderNumber(); ?> submitted on <?php print date('F j, Y g:i a', (int)$t_order->get('created_on'), array('GET_DIRECT_DATE' => true)); ?> has been completed.</p>

<p>Log in at <?php print $this->getVar('login_url'); ?> to view your orders under <em>My Account</em>.</p>