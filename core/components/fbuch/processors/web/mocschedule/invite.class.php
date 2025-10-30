<?php

include_once ('base.class.php');

/**
 * Class mocInviteProcessor
 */
class mocInviteProcessor extends mocBaseProcessor {

    /**
     * Invites a Member to a Chat Room
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

        if ($this->date_object && $this->name_object) {
            $invite_action = $modx->getOption('invite_action', $scriptProperties, '');
            $room_id = $this->date_object->get('riot_room_id');
            $riot_user_id = $this->name_object->get('riot_user_id');

            if ($this->run && !empty($riot_user_id) && empty($room_id)) {
                $rescheduled = $this->reschedule();

                $this->run->addError('keine riot_room_id', array(
                    'riot_room_id' => $room_id,
                    'date_id' => $this->date_object->get('id'),
                    'rescheduled?' => $rescheduled));

            }

            if (!empty($room_id) && !empty($riot_user_id)) {
                $result = $this->moc->login();
                //echo '<pre>' . print_r($result, 1) . '</pre>';
                $result = $this->api->invite($room_id , array('user_id' => $riot_user_id));
                $results['invite'] = $result;
                //echo '<pre>' . print_r($result, 1) . '</pre>';
                //die();
                if (isset($result['status'])) {
                    switch ($result['status']) {
                        case '200':
                            //all is fine
                            $this->date_object->set('matrix_members_kicked',0);
                            $this->date_object->save();
                            break;
                        case '429':
                            $rescheduled = $this->reschedule();
                            $this->run->addError('rate-limited request', array(
                                'riot_room_id' => $room_id,
                                'date_id' => $this->date_object->get('id'),
                                'rescheduled?' => $rescheduled));
                            break;
                        default:
                            $this->run->addError('Fehler', array(
                                'riot_room_id' => $room_id,
                                'date_id' => $this->date_object->get('id')));
                            break;

                    }


                } else {
                    $rescheduled = $this->reschedule();

                    $this->run->addError('Server hat nicht geantwortet', array(
                        'riot_room_id' => $room_id,
                        'date_id' => $this->date_object->get('id'),
                        'rescheduled?' => $rescheduled));

                }
            } elseif ($invite_action == 'riotinvite_invites') {
                $email = $this->fbuch->getNameEmail($this->name_object);
                if (!empty($email)) {
                    //$result = $moc->login();
                    //echo '<pre>' . print_r($result, 1) . '</pre>';
                    //$result = $api->invite($room_id, array('address' => $email));
                    //echo '<pre>' . print_r($result, 1) . '</pre>';
                    //die();
                    if (isset($result['status']) && $result['status'] == 200) {

                    }
                }

            }
        }


        return array('message' => json_encode($results));
    }
}

return 'mocInviteProcessor';
