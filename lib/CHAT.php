<?php
class CHAT extends CHAT_DB_MANIPULATION implements RESPONSE
{
    protected $responseArr = null;

    public function response()
    {
        if (!empty($this->responseArr))
        {
            return $this->responseArr;
        }

        return false;
    }
   
    public function save_sent_message($receiver, $sender, $message)
    {

        $this->save_message($receiver, $sender, $message);
        $this->responseArr['sent'] = true;
        return $this;
    }

    public function get_all_chat($receiver, $sender)
    {
      
        $this->responseArr['chat'] = $this->get_all_chat_messages($receiver, $sender);
        $this->responseArr['received'] = true;
        return $this;
    }

    public function delete_all_chat($receiver, $sender)
    {
        $delete_chat = $this->delete_all_chat_messages($receiver, $sender);
        
        if (empty($delete_chat))
        {
            $this->responseArr['chat_deleted'] = false;
        }
        else
        {
            $this->responseArr['chat_deleted'] = true;

        }

        return $this;
      
    }

    public function get_unread_chat($receiver, $sender)
    {

        $this->responseArr['chat'] = $this->get_unread_chat_messages($receiver, $sender);
        $this->responseArr['received'] = true;
        return $this;
    }


    public function save_read_messages($receiver, $sender)
    {

        $this->confirm_messages($receiver, $sender);
        $this->responseArr['read'] = true;
        return $this;
    }


    public function get_unread_user_list($receiver)
    {

        $this->responseArr['unread_user_list'] =  $this->get_all_unread_senders($receiver);
        return $this;
    }
}