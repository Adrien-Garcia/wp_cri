<?php
/**
 * Description of dashbord.php
 * @package wp_cridon
 * @subpackage views
 * @author Etech
 * @contributor Joelio
 */

global $current_user;

?>
<h1>Bienvenue, <i><?php echo $users->display_name ?></i> sur votre espace membre</h1>
<hr>
<p>
	<a href="<?php echo wp_logout_url(); ?>" title="Se deconnecter">Se deconnecter</a>
</p>
