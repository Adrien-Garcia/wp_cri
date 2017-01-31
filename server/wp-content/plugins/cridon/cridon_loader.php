<?php

class CridonLoader extends MvcPluginLoader
{

    protected $db_version   = '1.0';
    protected $tables       = array();

    public function activate()
    {

        // This call needs to be made to activate this app within WP MVC

        $this->activate_app(__FILE__);

        $this->migrate();
    }

    public function migrate()
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $this->db_version = get_option('cridon_db_version');

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

        // list of all versions
        $listVersions = array();
        $versions = $wpdb->get_results($wpdb->prepare(
            "SELECT `version` FROM " . $this->tables['plugin_migrations'] . " WHERE plugin = %s",
            $pluginName
        ));
        foreach($versions as $version) {
            array_push($listVersions, (int) $version->version);
        }

        //Search for migration files
        if (is_dir($pluginPath)) {
            if ($handle = opendir($pluginPath)) {
                $updates = array();
                while (false !== ($migration = readdir($handle))) {
                    if ($migration != "." && $migration != ".." ) {
                        $current = substr($migration, 0, 3);
                        // only check if the version is not exist
                        if (!in_array((int) $current, $listVersions)) {
                            $updates[$current] = file_get_contents($pluginPath.DIRECTORY_SEPARATOR.$migration);
                        }
                    }
                }
                if (!empty($updates)) {
                    ksort($updates);
                    foreach ($updates as $v => $sql) {
                        try {
                            // Remove all \n from the query
                            $sql = preg_replace('/\s+/', ' ', $sql);
                            // Separate queries from one another
                            // The counterpart is that we cannot have any queries containing a ';'
                            $queries = explode(";", $sql);
                            if (empty($queries)){
                                writeLog('query does not contain any \';\'  -> query : '.$sql, 'executeMigrationsSQL.log');
                                echo 'An error occured while parsing the sql syntax';
                                die();
                            }

                            foreach($queries as $query){
                                // Last row in array after explode can be a single space.
                                $query = trim($query);
                                if (empty($query)){
                                    continue;
                                }

                                // We keep `if` statements and not `elseif` so if we forget a `;` for queries separator, all of the queries would still be executed.
                                if (preg_match_all("|DROP TABLE ([a-zA-Z0-9`_\s]*)|", $query, $matches)) { // drop
                                    if (!empty($matches[0])) {
                                        foreach ($matches[0] as $update) {
                                            if ($update) {
                                                $wpdb->query($update);
                                            }
                                        }
                                    }
                                }
                                if (preg_match("/^CREATE\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF NOT EXISTS\s+)?([^\s]+)/i", $query, $matches)) {
                                    dbDelta($query);
                                }
                                //If ALTER QUERY
                                if (preg_match_all("|ALTER TABLE ([a-zA-Z0-9`_\s(),]*)|", $query, $matches)) {
                                    if (!empty($matches[0])) {
                                        foreach ($matches[0] as $alter) {
                                            $wpdb->query($alter);
                                        }
                                    }
                                }

                                if (preg_match_all("|(UPDATE ([a-zA-Z0-9`_\s()={}':\";\-éèà@ùê&\'\.]*);)|", $query, $matches)) {
                                    if (!empty($matches[0])) {
                                        foreach ($matches[0] as $update) {
                                            $wpdb->query($update);
                                        }
                                    }
                                }
                                if (preg_match_all("|TRUNCATE ([a-zA-Z0-9`_\s()]*)|", $query, $matches)) { // truncate
                                    if (!empty($matches[0])) {
                                        foreach ($matches[0] as $update) {
                                            if ($update) {
                                                $wpdb->query($update);
                                            }
                                        }
                                    }
                                }
                                if (preg_match_all("|INSERT ([a-zA-Z0-9`_\s(),':\";\-\/\\\\éèà@ùê&\'\.]*)|", $query, $matches)) { // insert
                                    if (!empty($matches[0])) {
                                        foreach ($matches[0] as $update) {
                                            if ($update) {
                                                $wpdb->query($update);
                                            }
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            writeLog($e->getMessage(), 'executeMigrationsSQL.log');
                            echo 'An error occured while executing a query';
                            die();
                        }

                        // Update last known version
                        $wpdb->insert($this->tables['plugin_migrations'], array(
                            "plugin" => $pluginName,
                            "version" => $v
                        ));
                    }
                    $this->db_version = (int) array_pop(array_keys($updates));
                }
                closedir($handle);
            }
        }

        $roles = get_option('cri_user_roles');
        foreach ( $roles as $k=>$v ){
            $exceptions = array( CONST_OFFICES_ROLE,CONST_ORGANISMES_ROLE,CONST_CLIENTDIVERS_ROLE );
            if( in_array($k, $exceptions) ) {
                $role = get_role($k);
                $role->remove_cap( 'read' );
                $role->remove_cap( 'level_0' );
            }
        }

        // remove specific role
        remove_role( CONST_OFFICES_ROLE );
        remove_role( CONST_ORGANISMES_ROLE );
        remove_role( CONST_CLIENTDIVERS_ROLE );

        // add custom caps to admincridon
        CriSetAdminCridonCaps();

        // update download_url field in cri_document when it's empty
        updateEmptyDownloadUrlFieldsDocument();

        update_option('cridon_db_version', $this->db_version);
        echo 'The execution of all queries is successful';
    }

    public function deactivate()
    {

        // This call needs to be made to deactivate this app within WP MVC

        $this->deactivate_app(__FILE__);

        // Perform any databases modifications related to plugin deactivation here, if necessary

    }

}
