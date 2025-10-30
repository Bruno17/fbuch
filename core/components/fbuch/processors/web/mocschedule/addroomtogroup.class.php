<?php

include_once ('base.class.php');

/**
 * Class mocAddRoomToGroupProcessor
 */
class mocAddRoomToGroupProcessor extends mocBaseProcessor {

    /**
     * Adds a Chat Room to the Club Community (Group)
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
        $group = $groups['vereins_mitglieder'];

        $results = array();

        if ($this->date_object) {
            $riot_room_id = $this->date_object->get('riot_room_id');

            $result = $this->moc->login($group);
            $fields = array('m.visibility' => array('type' => 'private'));
            $result = $this->api->addRoomToGroup($riot_room_id, $group['group'], $fields);
            $results['addRoomToGroup'] = $result;
            
            $status = '';
            if (isset($results['addRoomToGroup']['status'])) {
                $status = $results['addRoomToGroup']['status'];
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

return 'mocAddRoomToGroupProcessor';
