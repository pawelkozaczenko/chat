    var handleChatModalUserList = function(){

        var chatUserName = null;
        var loadUserList = null;
        var intervalUserID = null;
        var logged = null;
        var pending = null;

        var chatID = null;


        var userListFrameContent = $('#chatModal').find('.chat').eq(1);

        //for previos retrieveAllUsersStatus but this was changed 231 339 API_REQUEST file
        /*var formatResponse = function(data)
        {
           
            var formatResponse = {};
           
            for (user in data.other_user_list_with_status)
            {
                if (data.other_user_list_with_status[user] == 1)
                {
                    logged = true;
                }
                else
                {
                    logged = false
                }

                if (data.unread_user_list && data.unread_user_list.indexOf(user) !== -1)
                {
                    pending = true;
                }
                else
                {
                     pending = false;
                }
                formatResponse[user] = {logged: logged, pending: pending};
                
                 
            }

            return formatResponse;
        }*/

        var refreshUserList = function()
        {
            if (loadUserList){
                loadUserList = false;
                intervalUserID = setTimeout(getUsersList, 5*1000);
                
            }
            else{
                clearTimeout(intervalUserID);
            }
            
        }

        var getUsersList = function()
        {
            
            
            chatUserName =  $('#inputUserName').val();

            var dataObj = {
                name: chatUserName,
                method: 'retrieveAllUsersStatus'
            }

            if (!loadUserList && chatModalIsVisible())
            {

                ajaxRecipe(dataObj).done( function(data, textStatus, jqXHR)
                {
                   //API_REQUEST was changed we do not need this 
                   //var userList = formatResponse(data);
                   userList = data.other_user_list_with_status;
                   console.log(JSON.stringify(userList));
                   new buildUserList(bindChatUserClick).initialize(userList);
                   loadUserList = true;
                   refreshUserList();

                
                });

            }

        }

        var bindChatUserClick = function()
        {

            userListFrameContent.find('li').each(function(){
                $(this).bind('click', activateChatWithUser);
            });
        }
 

        this.setEvents = function()
        {

            $('#chatModal').on('shown.bs.modal', getUsersList);

        }

        ///==================================///
        ///connect with ChatBoxEvents========///
        ///==================================///

        var activateChatWithUser = function()
        {
            var localUser = chatUserName;
            var guestUser = $(this).find('strong').html();
            if (chatID)
            {
                chatID.initializeDeActivateInputChat();
            }
            chatID = new handleChatModalChatBox();
            chatID.initializeChatWithUser(localUser, guestUser);

            
        }


    }

  
    var initializeChatEvents = function(){
        new handleChatModalUserList().setEvents();

    }