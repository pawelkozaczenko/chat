<?php
class USER extends USER_DB_MANIPULATION implements RESPONSE
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


    public function log_user($name)
    {

        //so no such user
        $user_name_is_avaiable = $this->check_user_name_avaiability($name);


        if ($user_name_is_avaiable)
        {
            $this->insert_new_user_and_log_query($name);
            $logged = true;
        }
     
        else
        {
            $user_is_logged = $this->check_user_is_logged($name);

            if ($user_is_logged)
            {
                $this->responseArr['info'] = 'USER LOGGED';
                $logged = false;
            }

            else
            {
                $this->log_user_query($name);
                $logged = true;
            }
     
        }

        $this->responseArr['logged'] = $logged;
        if (!$logged)
        {
            $this->responseArr['logged_user_list'] = $this->get_all_users_logged();
        }
        else
        {
            $this->responseArr['other_user_list'] = $this->get_all_other_users($name);
        }

        return $this;

    }

    public function loggout_user($name)
    {

        $user_is_logged = $this->check_user_is_logged($name);

            if ($user_is_logged)
            {
                $this->loggout_user_query($name); 
            }

            $this->responseArr['outlogged'] = true;

        return $this;

    }

    public function delete_user($name)
    {

        $this->delete_user_query($name);
        $this->responseArr['user_deleted'] = true;

        return $this;

    }


    public function get_all_users_with_status($name)
    {
        $user_is_logged = $this->check_user_is_logged($name);

        if (!$user_is_logged)
        {
            $this->responseArr['info'] = 'USER NOT LOGGED';
        }
        else 
        {
            $this->responseArr['other_user_list_with_status'] = $this->get_all_other_users_with_status($name);
        }

        return $this;

    }

    public function get_all_users_with_status_and_pending($name)
    {
        $user_is_logged = $this->check_user_is_logged($name);

        if (!$user_is_logged)
        {
            $this->responseArr['info'] = 'USER NOT LOGGED';
        }
        else 
        {
            $this->responseArr['other_user_list_with_status'] = $this->get_all_other_users_with_status_and_pending($name);
        }

        return $this;

    }
}