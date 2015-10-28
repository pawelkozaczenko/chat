<?php
class API_REQUEST{

    private $ajaxRequestArr = null;
    private $apiResponseArr = null;

    public function __construct($ajaxRequest)
    {
        if (!empty($ajaxRequest) && is_string($ajaxRequest))
        {
            $this->ajaxRequestArr = json_decode($ajaxRequest, true);
        }
       
    }

    public function invoke_api_method()
    {
        //** ajax object attribute name holds the name of the function this is set in javascript js object **/

        $api_function_name = $this->ajaxRequestArr['method'];
     
        return $this->$api_function_name();

    }

    public function logUser()
    {
        $login = new USER();


        $db_query_error = $login->log_user($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $login->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }

    public function loggoutUser()
    {
        $login = new USER();


        $db_query_error = $login->loggout_user($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $login->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }


    public function deleteUser()
    {
        $login = new USER();


        $db_query_error = $login->delete_user($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $login->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }

    public function saveMessage()
    {
        $chat = new CHAT();

        $db_query_error = $chat->save_sent_message($this->ajaxRequestArr['receiver'], $this->ajaxRequestArr['sender'], $this->ajaxRequestArr['message'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $chat->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }


    public function receivedMessages()
    {
        $chat = new CHAT();

        $db_query_error = $chat->save_read_messages($this->ajaxRequestArr['receiver'], $this->ajaxRequestArr['sender'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $chat->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }

    public function getAllChat()
    {
        $chat = new CHAT();

        $db_query_error = $chat->get_all_chat($this->ajaxRequestArr['receiver'], $this->ajaxRequestArr['sender'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $chat->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }

     public function getUnreadChat()
    {
        $chat = new CHAT();

        $db_query_error = $chat->get_unread_chat($this->ajaxRequestArr['receiver'], $this->ajaxRequestArr['sender'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $chat->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }

     public function deleteAllChat()
    {
        $chat = new CHAT();

        $db_query_error = $chat->delete_all_chat($this->ajaxRequestArr['receiver'], $this->ajaxRequestArr['sender'])->error();


            if (!$db_query_error)
            {

                $this->apiResponse = $chat->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }

    public function pendingUsers()
    {
        $chat = new CHAT();

        $db_query_error = $chat->get_unread_user_list($this->ajaxRequestArr['receiver'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $chat->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;

    }

    /*public function retrieveAllUsersStatus()
    {
        //CHENGE THIS !!!
        //TOO BIG MESS IN API the request will be transformed in javascipt


        $login = new USER();


        $db_query_error = $login->get_all_users_with_status($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {

                $login_res = $login->response();
                $login_res_val = $login_res['other_user_list_with_status'];

            }
            else
            {
                $this->apiResponse = $db_query_error;
                return $this;


            }


 
        $chat = new CHAT();

        $db_query_error = $chat->get_unread_user_list($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {
                $chat_res = $chat->response();
                $chat_res_val = $chat_res['unread_user_list'];

                foreach ($login_res_val as $key => $value) {
                   $login_res_val[$key] = ['logged' => (bool)$value, 'pending' => in_array($key, $chat_res_val)];
                }

            }
            else
            {
                $this->apiResponse = $db_query_error;
                return $this;

            }

        $login_res['other_user_list_with_status'] = $login_res_val;
        $this->apiResponse = $login_res;
        return $this;


    }*/

    /*public function retrieveAllUsersStatus()
    {
        //CHENGE THIS !!!
        //TOO BIG MESS IN API the request will be transformed in javascipt


        $login = new USER();


        $db_query_error = $login->get_all_users_with_status($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {

                $login_res = $login->response();
                foreach ($login_res as $key => $value) {
                   $this->apiResponse[$key] = $value;
                }
            }
            else
            {
                $this->apiResponse = $db_query_error;
                return $this;


            }


 
        $chat = new CHAT();

        $db_query_error = $chat->get_unread_user_list($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {
                $chat_res = $chat->response();
                foreach ($chat_res as $key => $value) {
                   $this->apiResponse[$key] = $value;
                }

            }
            else
            {
                $this->apiResponse = $db_query_error;
                return $this;

            }

        return $this;


    }*/

    public function retrieveAllUsersStatus()
    {
        $login = new USER();

        $db_query_error = $login->get_all_users_with_status_and_pending($this->ajaxRequestArr['name'])->error();

            if (!$db_query_error)
            {

                $this->apiResponse = $login->response();

            }
            else
            {
                $this->apiResponse = $db_query_error;

            }


        return $this;
    }

    public function show_api_response()
    {
        echo json_encode($this->apiResponse);

    }


}