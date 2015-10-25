<?php
abstract class CHAT_DB_MANIPULATION extends DB_MANIPULATION
{
   
    private $unread_senders_array = null;
    private $unread_chat_messages = null;
    private $all_chat_messages = null;

    protected $save_message = "INSERT INTO `messages` (sender,receiver,send,message) VALUES (:sender,:receiver,NOW(),:message)";
    protected $confirm_message = "UPDATE `messages` SET received = NOW() WHERE sender = :sender AND receiver = :receiver";
    protected $select_unread_chat = "SELECT * FROM `messages` WHERE received = '00-00-00 00:00:00' AND (receiver = :receiver AND sender = :sender)";
    protected $select_all_chat = "SELECT * FROM `messages` WHERE (receiver = :receiver AND sender = :sender) OR (receiver = :sender AND sender = :receiver)";
    protected $delete_all_chat = "DELETE FROM `messages` WHERE (receiver = :receiver AND sender = :sender) OR (receiver = :sender AND sender = :receiver)";
    protected $select_unread_senders = "SELECT sender FROM `messages` WHERE received = '00-00-00 00:00:00' AND receiver = :receiver GROUP BY sender";


    private function manipulate_message_query($sql, $receiver, $sender = NULL, $message = NULL)
    {
     
        $query = $this->conn->prepare($sql);
        $query->bindParam(':receiver', $receiver,  PDO::PARAM_STR, 20);
        if (!empty($sender))
        {
            $query->bindParam(':sender', $sender,  PDO::PARAM_STR, 20);
        }
        if (!empty($message))
        {
            $query->bindParam(':message', $message,  PDO::PARAM_STR);
        }
        $query->execute();
        
           if (!$query)
           {
               $this->error = 'MSQL ERR: cannot insert/update message';
               return null;
           }

           return true;
    }

    private function select_message_users_query($sql, $receiver)
    {
        $query = $this->conn->prepare($sql);
        $query->bindParam(':receiver', $receiver,  PDO::PARAM_STR, 20);
        $query->execute();
        
        if (!$query)
        {
           $this->error =  'MSQL ERR: cannot retrieve data from database';
           return null;
        }

        
        if ($query->rowCount() > 0)
        {

            $res = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($res as $row)
            {
                $result_array[] = $row['sender'];
            }

            return $result_array;
        }

           return null;

    }

    private function select_chat_query($sql, $receiver, $sender)
    {
        $query = $this->conn->prepare($sql);
        $query->bindParam(':receiver', $receiver,  PDO::PARAM_STR, 20);
        $query->bindParam(':sender', $sender,  PDO::PARAM_STR, 20);
        $query->execute();
        
        if (!$query)
        {
           $this->error = 'MSQL ERR: cannot retrieve data from database';
           return null;
        }

        
        if ($query->rowCount() > 0)
        {

            $res = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($res as $row)
            {
                $result_array[] = $row;
            }

            return $result_array;
        }

           return null;

    }

     private function delete_chat_query($sql, $receiver, $sender)
    {
        $query = $this->conn->prepare($sql);
        $query->bindParam(':receiver', $receiver,  PDO::PARAM_STR, 20);
        $query->bindParam(':sender', $sender,  PDO::PARAM_STR, 20);
        $query->execute();
        
        if (!$query)
        {
           $this->error = 'MSQL ERR: cannot delete data from database';
           return null;
        }

        return true;

    }

     private function select_unread_senders_array($receiver)
    {

        $this->unread_senders_array = $this->select_message_users_query($this->select_unread_senders, $receiver);

           return $this;

    }


    protected function save_message($receiver, $sender, $message)
    {

        $sql = $this->save_message;
 
        $this->manipulate_message_query($sql, $receiver, $sender, $message);

        return $this;
        
        

    }

    //inne tez tak zrobic jak ta fukcja polikwisowac funkce pozniej !!!

    protected function get_all_chat_messages($receiver, $sender)
    {
        //** some tiny optimilaziation it this->chat  array if filled we do not need do mysql query **//

        if (empty($this->all_chat_messages))
        {
            $this->all_chat_messages = $this->select_chat_query($this->select_all_chat, $receiver, $sender);
        }
        return $this->all_chat_messages;

    }


    protected function delete_all_chat_messages($receiver, $sender)
    {

        return $this->delete_chat_query($this->delete_all_chat, $receiver, $sender);
    }



    protected function get_unread_chat_messages($receiver, $sender)
    {
        //** some tiny optimilaziation it this->chat  array if filled we do not need do mysql query **//

        if (empty($this->unread_chat_messages))
        {
            $this->unread_chat_messages = $this->select_chat_query($this->select_unread_chat, $receiver, $sender);
        }
        return $this->unread_chat_messages;
    }


    protected function confirm_messages($receiver, $sender)
    {

        $sql = $this->confirm_message;
 
        $this->manipulate_message_query($sql, $receiver, $sender);

        return $this;
        
    }


    protected function get_all_unread_senders($receiver)
    {
        //** some tiny optimilaziation it this->unreade_receivers array if filled we do not need do mysql query **//

        if (empty($this->unread_senders_array))
        {
            $this->select_unread_senders_array($receiver);
        }
        return $this->unread_senders_array;
    }

}