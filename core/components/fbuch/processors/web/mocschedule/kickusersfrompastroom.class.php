<?php

include_once ('base.class.php');

/**
 * Class mocKickUsersFromPastRoomProcessor
 */
class mocKickUsersFromPastRoomProcessor extends mocBaseProcessor {

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

        $results = array();

        if ($this->date_object) {
            $results = $this->moc->kickUsersFromRoom($this->date_object);
            
            $status = '';
            if (isset($results['kickUsersFromRoom']) && isset($results['kickUsersFromRoom']['status'])) {
                $status = $results['kickUsersFromRoom']['status'];
            }

            switch ($status) {
                case '200':
                    $this->date_object->set('matrix_members_kicked',1);
                    $this->date_object->save();
                    break;
                case '':
                    case '401':    
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

return 'mocKickUsersFromPastRoomProcessor';
