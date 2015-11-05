<?php

/**
 * Class Solde
 * @author Etech
 * @contributor Joelio
 */
class Solde extends MvcModel
{

    /**
     * @var string
     */
    public $table = '{prefix}solde';

    /**
     * Get solde by client number
     *
     * @param int $clientNumber
     * @return mixed
     */
    public function getSoldeByClientNumber($clientNumber)
    {
        global $wpdb;

        $sql = "SELECT `cs`.`quota`, `cs`.`type_support`, `cs`.`nombre`, `cs`.`points`, `cs`.`date_arret`, `cp`.points totalPoint
                FROM `{$wpdb->prefix}solde` cs
                INNER JOIN
                (
                    SELECT SUM(points) points, `client_number`, `date_arret` FROM `{$wpdb->prefix}solde`
                    WHERE `client_number` = '{$clientNumber}'
                    GROUP BY `date_arret`
                ) cp ON `cs`.`client_number` = `cp`.`client_number` AND `cs`.`date_arret` = `cp`.`date_arret`
                INNER JOIN
                (
                    SELECT MAX(`date_arret`) `date_arret`, `client_number` FROM `{$wpdb->prefix}solde`
                    GROUP BY `client_number`
                ) cd ON (`cs`.`client_number` = `cd`.`client_number` AND `cs`.`date_arret` = `cd`.`date_arret`)
                WHERE `cs`.`client_number` = '{$clientNumber}'";

        return $wpdb->get_results($sql);
    }

}