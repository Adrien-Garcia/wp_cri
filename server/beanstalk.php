<?php 

/*require 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
Mage::app()->getStore()->setConfig('dev/log/active', true);
*/
// => on pourrait passer le WP en maintenance

if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

function _log($message, $echo=true) { // ATTENTION à repasser à false
	//Mage::log($message, null, 'beanstalk.log');
	// => à remplacer par un log wp
	if ($echo) {
		if (is_array($message)) {
			foreach ($message as $line) {
				echo $line."<br/>";
			}
		} else {
			echo $message."<br/>";
		} 
	}
}


/*
 *  Vérification de l'adresse IP appelante 
 */
$autorized_ips = array();
$autorized_ips[] = '127.0.0.1';
$autorized_ips[] = '::1';
$autorized_ips [] = '195.28.202.129'; // IP Addonline 
for ($i=48; $i<=79; $i++) {
	$autorized_ips [] = '50.31.156.'.$i; // IPs Beanstalk (1ère plage)
}
for ($i=108; $i<=122; $i++) {
	$autorized_ips [] = '50.31.189.'.$i; // IPs Beanstalk (2eme plage)
}
$remoteIp = @$_SERVER['REMOTE_ADDR'];
if (strpos ($remoteIp, '192.168.') ===0 || $remoteIp=='127.0.0.1') { //si on est derrière un proxy
	$remoteIp = @$_SERVER['HTTP_X_FORWARDED_FOR'];
}
if (count(explode(', ', $remoteIp))>1) { //si on est derrière un proxy qui ajoute , 127.0.0.1 
    $tmp = explode(', ', $remoteIp);
    $remoteIp = $tmp[0];
}
/*if (!in_array($remoteIp, $autorized_ips))
{
	_log("Tentative non autorisée ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
	die("TOP SECRET");
}*/


/*
 *  Traitement web hook
 */	
if (isset ($_GET['hook'])) {
	$hook = $_GET['hook'];
	
	if ($hook =='pre' ) {
		
		/*
		 *  AVANT le déploiement
  		 */	
		_log("Pre-Deployment depuis ".$remoteIp);
		
		//On lève le flag maintenance http://www.hongkiat.com/blog/wordpress-maintenance/ || http://sivel.net/2009/06/wordpress-maintenance-mode-without-a-plugin/
		file_put_contents(dirname(__FILE__).DS.'.maintenance', '<?php $upgrading = time(); ?>');

		_log("Pre-Deployment OK", true);
			
	} else if ($hook =='post' ) {

		/*
		 *  APRES le déploiement
  		 */	
		_log("Post-Deployment depuis ".$remoteIp);
		
		// flush cache
		$cacheDir = dirname(__FILE__) . DS . 'wp-content' . DS . 'cache';
		emptyDir($cacheDir. DS . 'min', false);
		emptyDir($cacheDir. DS . 'wp-rocket', false);
		
		
		/* symlink needed packages */
		
		// try with "npm link" command => result in "EPERM" issues
		/*if( !is_dir( dirname(__FILE__) . DS . "gulp/node_modules" ) ) {
		    
    		$packageJson = json_decode(file_get_contents(dirname(__FILE__) . DS . "gulp/package.json"));
    		$cmd = "npm link";
    		foreach ($packageJson->devDependencies as $dep => $v) {
    		    //_log($dep);
    		    $cmd .= " " . $dep;
    		}
    		$cmd .= " 2>&1";
    		
    		// change to gulp local directory before executing "npm link pck1 pck2..." command
    		chdir(dirname(__FILE__) . DS . "gulp");
    		
    		$output = array();
    		$output[] = "exec >> ".$cmd;
    		$result;
    		exec($cmd, $output, $result);
    		if ($result!=0) {
    		    $output[]="<h2>Erreur while symlinking gulp packages :</h2><br/>$result";
    		    _log($output, true);
    		    //die();
    		}
    		
		}*/
		
		// simply use the ln command to symlink the node_modules directory
		$cmd = "";		
		if( file_exists( dirname(__FILE__) . DS . "gulp/node_modules" ) ) {
		    $cmd .= "rm -rf node_modules && ";
		}
            
	    $cmd .= "ln -s /usr/local/lib/node_modules";
        $cmd .= " 2>&1";
		
		// change to gulp local directory before executing "npm link pck1 pck2..." command
		chdir(dirname(__FILE__) . DS . "gulp");
		
		$output = array();
		$output[] = "exec >> ".$cmd;
		$result;
		exec($cmd, $output, $result);
		if ($result!=0) {
		    $output[]="<h2>Erreur while symlinking gulp packages :</h2><br/>$result";
		    _log($output, true);
		    die();
		}
		
		
		// execute gulp task
		$cmd = "gulp prod 2>&1";
		$output = array();
		$output[] = "exec >> ".$cmd;
		exec($cmd, $output, $result);
		if ($result!=0) {
		    $output[]="<h2>Error while executing gulp tasks :</h2><br/>$result";
		    _log($output, true);
		    die();
		}
		
		//On baisse le flag maintenance
		unlink (dirname(__FILE__).DS.'.maintenance');
		
		_log("Post-Deployment OK", true);
				
	} else {
		_log("Tentative non conforme ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
		die("TOP SECRET");
	}	
} else {
	_log("Tentative non conforme ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
	die("TOP SECRET");
	
}



function emptyDir($cacheDir, $deleteMe) {
	
	if(!$dh = @opendir($cacheDir)) return;
	while (false !== ($obj = readdir($dh))) {
		if($obj=='.' || $obj=='..') continue;
		if($obj=='.gitignore' || $obj=='readme.txt') continue;
		if (!@unlink($cacheDir.'/'.$obj)) emptyDir($cacheDir.'/'.$obj, true); 
	}
	closedir($dh);
	if ($deleteMe){
		@rmdir($cacheDir);
	}
}