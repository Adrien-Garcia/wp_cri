<?php
/**
 * Description of majnotaryrole.class.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

/**
 * Class MajNotaryRole
 */
class MajNotaryRole
{
    /**
     * @var mixed : wpdb global var
     */
    protected static $wpdb;

    /**
     * @var int : number total of items
     */
    protected static $nbItems;

    /**
     * @var int : limit of processing block
     */
    protected static $limit = 1000;

    /**
     * @var int : offset of query limit
     */
    protected static $offset = 0;

    /**
     * Init action
     */
    public static function init()
    {
        global $wpdb;

        try {
            // show starting message
            echo "Maj Notary role starting, please wait...\n";

            // set global var
            self::$wpdb = $wpdb;

            // init flag
            $i = 1;

            // nb items
            self::setNbItems();

            // set role
            self::setDefaultRole($i);

            // success output msg
            echo "Action done \n";
        } catch (\Exception $e) {
            // output error message
            echo $e->getMessage() . "\n";
        }
    }

    /**
     * Get number total of items
     *
     * @return void
     */
    public function setNbItems()
    {
        $sql = 'SELECT COUNT(*) as NB FROM ' . self::$wpdb->prefix . 'notaire';

        $res = self::$wpdb->get_row($sql);

        self::$nbItems = ($res->NB) ? $res->NB : 0;
    }

    /**
     * @param int $i
     * @return void
     */
    public function setDefaultRole($i)
    {
        // set max limit
        $limitMax = intval(self::$nbItems / self::$limit) + 1;

        // repeat action until limit max
        if ($i <= $limitMax) {
            // free $limitMax var
            unset($limitMax);

            // query
            $sql = 'SELECT * FROM ' . self::$wpdb->prefix . 'notaire';
            $sql .= ' LIMIT ' . self::$limit . ' OFFSET ' . intval(self::$offset);

            // exec query
            $notaries = self::$wpdb->get_results($sql);
            // free $sql var
            unset($sql);

            // maj notary role
            self::addRole($notaries);
            /**
             * example call remove role
             */
//            self::removeRole($notaries, 'finance');
            // free $notaries var
            unset($notaries);

            // increments offset
            self::$offset += self::$limit;
            // increments flag
            $i ++;
            // call set role action
            self::setDefaultRole($i);
        }
    }

    /**
     * @param mixed $notaries
     * @return void
     */
    public function addRole($notaries)
    {
        // list not empty
        if (is_array($notaries) && count($notaries) > 0) {
            foreach ($notaries as $notary) {
                // get user by id
                $user = new WP_User($notary->id_wp_user);
                // user must be an instance of WP_User vs WP_Error
                if ($user instanceof WP_User) {
                    // default role
                    if (!in_array($notary->category, Config::$notaryNoDefaultOffice)) { // Categ OFF
                        $user->add_role(CONST_NOTAIRE_ROLE);
                    } else {
                        if ($notary->category == CONST_CLIENTDIVERS_CATEG) { // Categ DIV
                            $user->add_role(CONST_NOTAIRE_DIV_ROLE);
                        } elseif ($notary->category == CONST_ORGANISMES_CATEG) { // Categ ORG
                            $user->add_role(CONST_NOTAIRE_ORG_ROLE);
                        }
                    }
                    $rolesNotaire = Config::$notaryRolesByFunction['notaries'];
                    if (!empty($rolesNotaire[$notary->id_fonction])){
                        foreach ($rolesNotaire[$notary->id_fonction] as $role){
                            $user->add_role($role);
                        }
                    }
                }
            }
        }
    }

    /**
     * Removing roles
     * like a rollback action
     *
     * @param mixed $notaries
     * @param string $option : flag of specific role to be removed
     *
     * @return void
     */
    public function removeRole($notaries, $option = 'all')
    {
        // list not empty
        if (is_array($notaries) && count($notaries) > 0) {
            foreach ($notaries as $notary) {
                // get user by id
                $user = new WP_User($notary->id_wp_user);
                // user must be an instance of WP_User vs WP_Error
                if ($user instanceof WP_User) {
                    switch ($option) {
                        // default role
                        case 'notaire':
                            $user->remove_role(CONST_NOTAIRE_ROLE);
                            break;
                        // finance role
                        case 'finance':
                            $user->remove_role(CONST_FINANCE_ROLE);
                            break;
                        // all
                        default :
                            $user->remove_role(CONST_NOTAIRE_ROLE);
                            $user->remove_role(CONST_FINANCE_ROLE);
                            break;
                    }
                }
            }
        }
    }
}