<?php

class Demarche extends \App\Override\Model\CridonMvcModel
{

    public $display_field = 'date';
    public $table = '{prefix}demarche';
    public $belongs_to = array(
        'Session' => array(
            'foreign_key' => 'session_id'
        ),
        'Notaire' => array(
            'foreign_key' => 'notaire_id'
        ),
        'Formation' => array(
            'foreign_key' => 'formation_id'
        )
    );

    protected $_csv_format = array(
        'id' => '',
        'type' => '',
        'date_demande' => '',
        'details' => '',
        'commentaire_client' => '',
        'commentaire_cridon' => '',
        'notaire' => '',
        'notaire_crpcen' => '',
        'notaire_nom' => '',
        'notaire_prenom' => '',
        'notaire_mail' => '',
        'session' => '',
        'session_date' => '',
        'session_horaire' => '',
        'session_lieu' => '',
        'organisme_crpcen' => '',
        'organisme_name' => '',
        'formation' => '',
        'formation_titre' => '',
        'formation_matieres' => '',
    );

    public function createFromFormulaire($type, $currentUser, $content, $formationCommentaire, $element)
    {
        $date = new DateTime();
        $data = array(
            'type' => $type,
            'notaire_id' => $currentUser->id,
            'session_id' => $type === CONST_FORMATION_PREINSCRIPTION ? $element->id : 0,
            'formation_id' => $type === CONST_FORMATION_DEMANDE ? $element->id : ($type === CONST_FORMATION_PREINSCRIPTION ? $element->id_formation : 0),
            'details' => $content,
            'commentaire_client' => $formationCommentaire,
            'commentaire_cridon' => '',
            'date' => $date->format('Y-m-d'),
        );
        $this->create($data);
    }

    public function exportCsvDemarchesToFile($file_path, $with_header = true, $start_date = false, $end_date = false ) {
        if (!file_exists( dirname($file_path) )) {
            mkdir(dirname($file_path), 0777, true);
        }
        $resource = fopen($file_path, 'w+b');
        $this->exportCsvDemarches($resource , $with_header, $start_date, $end_date);
        fclose($resource);
    }

    public function exportCsvDemarches($resource , $with_header = true, $start_date = false, $end_date = false ) {
        $reg_date = '/^\d{4}[0-1]\d[0-3]\d$/';
        $options = array(
            'joins'=>array('Notaire')
        );
        if (!empty($start_date) && !empty($end_date)) {
            if (preg_match($reg_date, $start_date) && preg_match($reg_date, $end_date)) {
                $options['conditions'] = ' date >= "'.$start_date.'" AND date <= "'.$end_date.'" ';
            } else {
                throw new Exception('CSV export : date format error, must be "Ymd"');
            }
        }
        $demarches = $this->find($options);

        if ($with_header) {
            fputcsv($resource, array_keys($this->_csv_format));
        }
        $lines = array();
        foreach ($demarches as $demarche) {
            $formation = mvc_model('formation')->find_by_id($demarche->formation_id, array(
                'joins'=>array('Post')
            ));
            $session = mvc_model('session')->find_by_id($demarche->session_id, array(
                'joins'=>array('Entite')
            ));
            $matieres = mvc_model('Formation')->getMatieres($formation);
            $arrayMatieres = array();
            if ($matieres) {
                foreach ($matieres as $matiere) {
                    $arrayMatieres[] = $matiere->label;
                }
            } else {
                $arrayMatieres[] = "0";
            }

            $_line = array(
                'id' => $demarche->id,
                'type' => $demarche->type,
                'date_demande' => $demarche->date,

                'details' => $demarche->details,
                'commentaire_client' => $demarche->commentaire_client,
                'commentaire_cridon' => $demarche->commentaire_cridon,

                'notaire' => $demarche->notaire_id,

                'session' => $demarche->session_id,

                'formation' => $demarche->formation_id,
            );
            $_notaire = $_session = $_formation = array();
            if (!empty($demarche->notaire_id) && !empty($demarche->notaire)) {
                $_notaire = array(
                    'notaire_crpcen' => $demarche->notaire->crpcen,
                    'notaire_nom' => $demarche->notaire->last_name,
                    'notaire_prenom' => $demarche->notaire->first_name,
                    'notaire_mail' => $demarche->notaire->email_adress,
                );
            }
            if (!empty($demarche->session_id) && !empty($session)) {
                $_session = array(
                    'session_date' => $session->date,
                    'session_horaire' => $session->timetable,
                    'session_lieu' => $session->place,
                    'organisme_crpcen' => $session->entite->crpcen,
                    'organisme_name' => $session->entite->office_name,
                );
            }

            if (!empty($demarche->formation_id) && !empty($formation)) {
                $_formation = array(
                    'formation_titre' => $formation->post->post_title,
                    'formation_matieres' => implode('|', $arrayMatieres),
                );
            }

            $_line = array_merge($this->_csv_format, $_line, $_notaire, $_session, $_formation);

            $lines[] = $_line;
        }
        $lines = $this->_validateDataForCsv(array_keys($this->_csv_format), $lines);
        foreach ($lines as $line) {
            fputcsv($resource, $line);
        }

    }

    /**
     * Organize csv columns to be the same order as the header
     *
     * @param array $header array of each columns
     * @param array $data   array of arrays each being one csv line
     *
     * @throws Exception
     * @return array
     */
    private function _validateDataForCsv($header, $data) {
        $nbCol = count($header);
        $newData = array();
        foreach ($data as $key => $array) {
            if (count($array) !== $nbCol) {
                throw new Exception('Csv Export Exception : the line "'.$key.'" has an incorrect number of columns : expected '.$nbCol.' got '.count($array).'.');
            }
            $newData[$key] = array();
            foreach ($header as $hKey => $column) {
                if (!array_key_exists($column, $array)) {
                    throw new Exception('Csv Export Exception : the line "'.$key.'" miss the "'.$column.'" key.');
                }
                $newData[$key][] = !isset($array[$column]) ? '' : $array[$column];
            }
        }
        return $newData;
    }
}
