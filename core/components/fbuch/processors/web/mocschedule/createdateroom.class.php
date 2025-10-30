<?php

include_once ('base.class.php');

/**
 * Class mocCreateDateRoomProcessor
 */
class mocCreateDateRoomProcessor extends mocBaseProcessor {

    /**
     * Create a Chat Room for a Date
     *
     * @return mixed
     */
    public function process() {
        $modx = &$this->modx;

        if ($this->moc->riot_isactive) {

        } else {
            return '';
        }

        $this->api = &$this->moc->api;

        $groups = $this->moc->groups;
        $this->group = $groups['vereins_mitglieder'];

        $results = array();

        if ($this->date_object) {
            $results = $this->moc->createDateRoom($this->date_object);
            $status = '';
            if (isset($results['createRoom']) && isset($results['createRoom']['status'])) {
                $status = $results['createRoom']['status'];
            }
            if (isset($results['updateRoom']) && isset($results['updateRoom']['status'])) {
                $status = $results['updateRoom']['status'];
            }

            switch ($status) {
                case '200':
                    //all is fine
                    break;
                case '':
                    $rescheduled = $this->reschedule();
                    $this->run->addError('Server antwortet scheinbar nicht', array(
                        'date_id' => $this->date_object->get('id'),
                        'rescheduled?' => $rescheduled));
                    break;
                default:
                    $this->run->addError('Fehler', array('date_id' => $this->date_object->get('id')));
                    break;

            }

        }

        return array('message' => json_encode($results));
    }
}

return 'mocCreateDateRoomProcessor';
