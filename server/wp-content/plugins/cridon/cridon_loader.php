<?php

class CridonLoader extends MvcPluginLoader
{

    protected $db_version   = '1.0';
    protected $tables       = array();

    public function activate()
    {
        global $wpdb;

        // This call needs to be made to activate this app within WP MVC

        $this->activate_app(__FILE__);

        // Perform any databases modifications related to plugin activation here, if necessary

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        add_option('cridon_db_version', $this->db_version);

        $this->tables = array(
            'plugin_migrations' => $wpdb->prefix . 'plugin_migrations'
        );

        $basePath = dirname(__FILE__);
        $pluginName = array_pop(explode(DIRECTORY_SEPARATOR, $basePath));
        $pluginPath = $basePath.DIRECTORY_SEPARATOR.'migrations';

        $sql = "
            CREATE TABLE IF NOT EXISTS `" . $this->tables['plugin_migrations'] . "` (
              `id` int(10) NOT NULL AUTO_INCREMENT,
              `plugin` varchar(255) NOT NULL,
              `version` varchar(3) NOT NULL DEFAULT '000',
              `created_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            );
        ";
        // Use dbDelta() to create the tables for the app here
        dbDelta($sql);

        //Search for the latest version of the plugin (will be empty if the table has just been created)
        $max = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT MAX(version) FROM " . $this->tables['plugin_migrations'] . " WHERE plugin = %s",
            $pluginName
        ) );

        //Search for migration files
        if (is_dir($pluginPath)) {
            if ($handle = opendir($pluginPath)) {
                $updates = array();
                while (false !== ($migration = readdir($handle))) {
                    if ($migration != "." && $migration != ".." ) {
                        $current = substr($migration, 0, 3);
                        if (((int) $current) > $max) {
                            $updates[$current] = file_get_contents($pluginPath.DIRECTORY_SEPARATOR.$migration);
                        }
                    }
                }
                ksort($updates);
                foreach ($updates as $v => $sql) {
                    //TODO surround with try/catch
                    // Use dbDelta() to create the tables for the app here
                    dbDelta($sql);
                    // Update last known version
                    $wpdb->insert($this->tables['plugin_migrations'], array(
                        "plugin" => $pluginName,
                        "version" => $v
                    ));
                }
                closedir($handle);
            }
        }

	    // @TODO to be removed if we use specific plugin like "user-role-editor"
	    // add  notaire role
	    add_role( 'notaire', 'Notaire', array( 'read' => true, 'level_0' => true ) );

    }

    public function deactivate()
    {

        // This call needs to be made to deactivate this app within WP MVC

        $this->deactivate_app(__FILE__);

        // Perform any databases modifications related to plugin deactivation here, if necessary

    }

}