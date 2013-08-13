<?php
	$t_order = $this->getVar('t_order');
	$o_client_services_config = caGetClientServicesConfiguration();
?>
You were sent the following message by <em><?php print $this->getVar('sender_name'); ?></em> on <em><?php print date('F j, Y g:i a', $this->getVar('sent_on')); ?></em>:

<p>Your order <?php print $t_order->getOrderNumber(); ?> has been received and will be processed shortly. You will receive a follow-up message when your order is ready for payment.</p>

<p>Log in at <?php print $this->getVar('login_url'); ?> to view your orders under <em>My Account</em>.</p>