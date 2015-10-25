<?php
abstract class USER_DB_MANIPULATION extends DB_MANIPULATION
{
    private $users_array = null;
    private $other_users_array = null;
    private $other_users_array_with_status = null;
    private $other_users_array_with_status_and_pending = null;
    private $logged_users_array = null;


    protected $insert_new_user_and_log = "INSERT INTO users (name,log_user_date,logged) VALUES (:name,NOW(),1)";
    protected $log_existing_user = "UPDATE users SET logged ='1' WHERE name = :name";
    protected $loggout_existing_user = "UPDATE users SET logged ='0', log_user_date = NOW() WHERE name = :name";
    protected $select_existing_users = "SELECT * FROM `users` WHERE 1 ORDER BY logged, name DESC"; //order by logged 
    protected $select_logged_users = "SELECT name FROM `users` WHERE logged = 1";
    protected $delete_user = "DELETE FROM users WHERE name = :name";



    //combined query from two tables
    protected $select_existing_users_with_pending = "(SELECT u.name, u.logged, MIN(m.received) as received FROM `messages` as m JOIN `users` as u ON u.name = m.sender WHERE m.receiver = :name GROUP BY u.name) UNION (SELECT name, logged, NULL as received FROM `users` WHERE name <> :name AND name NOT IN (SELECT u.name FROM `messages` as m JOIN `users` as u ON u.name = m.sender WHERE m.receiver = :name GROUP BY u.name)) ORDER BY logged, name DESC";


    private function manipulate_users_query($sql, $name)
    {
     
        $query = $this->conn->prepare($sql);
        $query->bindParam(':name', $name,  PDO::PARAM_STR);
           $query->execute();
        
           if (!$query)
           {
               $this->error = 'MSQL ERR: cannot insert/update user';
               return null;
           }

           return true;
    }


   
    private function select_users_query($sql, $exceptName = null, $status = null, $name = null)
    {

        $result_array = array();

        $query = $this->conn->prepare($sql);

        if ($name)
        {
            $query->bindParam(':name', $name,  PDO::PARAM_STR);
        }

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
                if ($row['name'] !== $exceptName)
                {
                    if (!$status)
                    {
                        $result_array[] = $row['name'];
                    }
                    else if (!$name)
                    {
                        $result_array[$row['name']] = $row['logged'];
                    }
                    else
                    {
                        if ($row['received'] == '0000-00-00 00:00:00')
                        {
                            $pending = '1';
                        }
                        else
                        {
                            $pending = '0';
                        }

                        $result_array[$row['name']] = ['logged' => (bool)$row['logged'], 'pending' => (bool)$pending];

                    }
                }
            }

            return $result_array;
        }

           return null;

    }

     protected function get_all_users()
    {
        //** some tiny optimilaziation it this->users array if filled we do not need do mysql query **//

        if (empty($this->users_array))
        {
            $this->users_array = $this->select_users_query($this->select_existing_users);
        }
        return $this->users_array;
    }

    protected function get_all_other_users($name)
    {
        //** some tiny optimilaziation it this->other_users array if filled we do not need do mysql query **//

        if (empty($this->other_users_array))
        {
            $this->other_users_array = $this->select_users_query($this->select_existing_users, $name);
        }
        return $this->other_users_array;
    }

    protected function get_all_other_users_with_status($name)
    {
        if (empty($this->other_users_array_with_status))
        {
            $this->other_users_array_with_status = $this->select_users_query($this->select_existing_users, $name, true);
        }
        return $this->other_users_array_with_status;

    }

    protected function get_all_other_users_with_status_and_pending($name)
    {

        if (empty($this->other_users_array_with_status_and_pending))
        {
            $this->other_users_array_with_status_and_pending = $this->select_users_query($this->select_existing_users_with_pending, $name, true, $name);
        }
        return $this->other_users_array_with_status_and_pending;

    }

    protected function get_all_users_logged()
    {
        //** some tiny optimilaziation it this->users array if filled we do not need do mysql query **//

        if (empty($this->logged_users_array))
        {
            $this->logged_users_array = $this->select_users_query($this->select_logged_users);
        }
        return $this->logged_users_array;
    }



    protected function check_user_name_avaiability($name)
    {
        $this->get_all_users();

        if (is_array($this->users_array) && in_array($name, $this->users_array))
        {
            return false;
        }
        else
        {
            return true;
        }

    }


    protected function check_user_is_logged($name)
    {
        $this->get_all_users_logged();


        if (!empty($this->logged_users_array) && in_array($name, $this->logged_users_array))
        {
            return true;
        }
        else
        {
            return false;
        }

    }

   

    protected function log_user_query($name)
    {

        $sql = $this->log_existing_user;
 
        $this->manipulate_users_query($sql, $name);

           return $this;
        
        

    }

    protected function loggout_user_query($name)
    {

        $sql = $this->loggout_existing_user;
 
        $this->manipulate_users_query($sql, $name);

           return $this;
        
        

    }

    protected function insert_new_user_and_log_query($name)
    {

        $sql = $this->insert_new_user_and_log;

        $this->manipulate_users_query($sql, $name);


           return $this;
        

    }

    protected function delete_user_query($name)
    {

        $sql = $this->delete_user;

        $this->manipulate_users_query($sql, $name);

        return $this;
        

    }

}