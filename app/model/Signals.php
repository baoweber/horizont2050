<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

class Signals extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    /** @var Acknowledgements */
    private $acknow;

    public function __construct(\DibiConnection $connection, Acknowledgements $acknowledgements)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:signals';

        // assigning connection
        $this->db       = $connection;
        $this->acknow   = $acknowledgements;


        // defining schema
        $this->schema = array(
            'name'              => '%s',
            'title'             => '%s',
            'perex'             => '%s',
            'description'       => '%s',
            'impact'            => '%s',
            'likelyhood'        => '%s',
            'drivers'           => '%s',
            'recomendations'    => '%s',
            'categories_id'     => '%i',
            'event_types_id'    => '%i',
            'spaces_id'         => '%i',
            'relevance'         => '%i',
            'probability'       => '%i',
            'impacts_id'        => '%i',
            'scales_id'         => '%i',
            'image_path'        => '%s',
            'timeframe'         => '%s',
            'public'            => '%i',
            'ptree'             => '%s',
            'user_update'       => '%d',
            'user_create'       => '%d'
        );
    }

    /**
     * Overrides the the default get all method by addig array of acknowledgement to each item
     *
     * @param bool $params
     * @return array
     */
    public function getAll($params = false)
    {
        // calling the divine presenter logic for getting all signals
        $output = parent::getAll($params);

        // dding acknowledgements to each item
        if (isset($output) && $params['with-acknowledgements']) {
            $output = $this->addAcknowledgements($output);
        }

        $output = $this->calculateRelevance($output);

        return $output;
    }

    public function getAllSelectiveFields($fields, $activeoOnly = false, $withAcknowledgements = false)
    {
        $query[] = 'SELECT %n';
        $query[] = $fields;
        $query[] = 'FROM %n' ;
        $query[] = $this->table;

        if($activeoOnly) {
            $query[] = 'WHERE %and';
            $query[] = array('public%i' => 1);
        }

        $output = $this->db->fetchAll($query);

        // dding acknowledgements to each item
        if (isset($output) && $withAcknowledgements) {
            $output = $this->addAcknowledgements($output);
        }

        $output = $this->calculateRelevance($output);

        return $output;
    }


    /**
     * Overrides the the default get single method by addig array of acknowledgement to each item
     *
     * @param bool $params
     * @return array
     */
    public function getSingle($id)
    {
        // calling the divine presenter logic for getting all signals
        $output = parent::getSingle($id);

        // dding acknowledgements to each item
        if (isset($output->id)) {
            $output->acknowledgements = $this->acknow->getAllBySignal($output->id);
        }

        return $output;
    }

    /**
     * Activates given item
     *
     * @param $id
     */
    public function activate($id)
    {
        $this->setValue($id, 'public', 1);
    }

    /**
     * Deactivates given item
     *
     * @param $id
     */
    public function deactivate($id)
    {
        $this->setValue($id, 'public', 0);
    }

    /**
     * Update a single field of a given resource
     *
     * @todo move to divine presenter and bind to general update function
     * @param $id
     * @param $field
     * @param $value
     * @throws \Exception
     */
    public function setValue($id, $field, $value )
    {

        if(isset($this->schema[$field])) {

            $key = $field . $this->schema[$field];
            $values = array(
                $key => $value
            );
            $this->db->query("UPDATE %n SET %a WHERE `id` = %i ", $this->table, $values, $id);

        } else {
            throw new \Exception("Field `" . $field . "` is not in the model definition.");
        }

    }

    private function addAcknowledgements($output)
    {
        if(count($output)) {
            foreach($output as $item) {
                $item->acknowledgements = $this->acknow->getAllBySignal($item->id);
            }
        }

        return $output;
    }

    private function calculateRelevance($output)
    {
        foreach($output as $item) {
           if(isset($item->relevance) && isset($item->probability)) {
               $item->relevance = $this->getRelevanceStatus($item);
           }
        }

        return $output;

    }

    public function getRelevanceStatus($item)
    {
        $relevance = $item->relevance;
        if(in_array(intval($item->relevance), array(1,2,3)) && in_array(intval($item->probability), array(1,2,3))) {
            $relevance = intval($item->relevance) * intval($item->probability);
        }

        Debugger::barDump(array($item->relevance, $item->probability));

        switch ($relevance) {
            case (1) :
                $item->relevance = '1';
                break;
            case (2) :
                $item->relevance = '1';
                break;
            case (3) :
                $item->relevance = '2';
                break;
            case (4):
                $item->relevance = '2';
                break;
            case (6):
                $item->relevance = '3';
                break;
            case (9):
                $item->relevance = '3';
                break;
        }

        return $item->relevance;
    }
}


