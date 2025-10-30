<?php

include_once ('base.class.php');

/**
 * Class mocSendCommentProcessor
 */
class mocSendCommentProcessor extends mocBaseProcessor {

    /**
     * Send Comment to a Chat Room
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

        if ($this->date_object && ($this->name_object || $this->user_object)) {
            $room_id = $this->date_object->get('riot_room_id');
            if ($this->name_object){
                $riot_user_id = $this->name_object->get('riot_user_id');
                $name = $this->name_object->get('firstname') . ' ' . $this->name_object->get('name');                
            } elseif ($this->user_object){
                $name = $this->user_object->get('username');    
            }

            $comment_start = $this->getProperty('comment_start'); 
            $comment_start = empty($comment_start) ? 'Von ' : $comment_start;
            //$modx->getOption('comment_start', $scriptProperties, 'Von ');
            $comment = $comment_start . "$name ($riot_user_id): \n" . $this->getProperty('comment');

            if (!empty($room_id) && !empty($name)) {
                $result = $this->moc->login();
                //echo '<pre>' . print_r($result, 1) . '</pre>';
                $result = $this->api->send($room_id, 'm.room.message', array('body' => $comment, 'msgtype' => 'm.text'));
                //echo '<pre>' . print_r($result, 1) . '</pre>';
                //die();
                $results['sendComment'] = $result;
            $status = '';
            if (isset($results['sendComment']['status'])) {
                $status = $results['sendComment']['status'];
            }

            switch ($status) {
                case '200':
                    //all is fine
                    break;
                case '':
                case '401':    
                    $rescheduled = $this->reschedule();
                    $this->run->addError('Server antwortet scheinbar nicht', array(
                        'date_id' => $this->date_object->get('id'),
                        'rescheduled?' => $rescheduled));
                    break;
                case '429':    
                    $rescheduled = $this->reschedule();
                    $this->run->addError('Too Many Requests', array(
                        'date_id' => $this->date_object->get('id'),
                        'rescheduled?' => $rescheduled));
                    break;                    
                default:
                    $this->run->addError('Fehler', array('date_id' => $this->date_object->get('id')));
                    break;

            }  
            }
        }


        return array('message' => json_encode($results));
    }
}

return 'mocSendCommentProcessor';
