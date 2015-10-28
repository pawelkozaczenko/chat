    var handleChatModalChatBox = function(){
                //**resposnibsle for talk **//
        var chatUserName = null;
        var contactUserName = null;
        var loadChatBox = null;
        var intervalChatID = null;
        var intervalChatRedID = null;
        var initialChat = null;

        var sendMessage = null;

        var chatMessageBoxHeader = $('#chatModal').find('.panel-heading').eq(0);
        //var chatMessageBox = $('#chatModal').find('.panel-body').eq(0).children().eq(0);
        var chatMessageBox = $('#chatModal').find('.panel-body').eq(0).children('.chat');

        
        var chatInputFieldFrame = $('#chatModal').find('.panel-footer').eq(0).children().eq(0);
        var chatInputField = $('#chatModal').find('.panel-footer').find('input[type="text"]');
        var chatSendButton = $('#chatModal').find('.panel-footer').find('button');


        var markRedMessageBoxHeader = function()
        {
             //add red chat header and remove 
            chatMessageBoxHeader.addClass('red-background');
            intervalChatRedID = setTimeout(function(){
                chatMessageBoxHeader.removeClass('red-background');
                clearTimeout(intervalChatRedID);
            }, 1*1000);
        }

        var refreshChatBox = function()
        {
            if (loadChatBox){
                loadChatBox = false;
                intervalChatID = setTimeout(getChatMessage, 1*1000);
               
            }
            else{
                clearTimeout(intervalChatID);
            }
            
        }


        var confirmedReadData = function(messagesObject)
        {

            var dataObj = {
                sender: contactUserName,
                receiver: chatUserName,
                method: 'receivedMessages'
            }

            //just for any casee hard for debug xD
            if (chatUserName && contactUserName)
            {
                ajaxRecipe(dataObj).done(function(data, textStatus, jqXHR)
                {
                    new buildChatBox().displayMessagesList(messagesObject, chatUserName, contactUserName); 
                    refreshChatBox();
                   

                });

            }

        }




        var getChatMessage = function()
        {

            if (initialChat)
            {
                var method = 'getAllChat';


            }
            else
            {
                var method = 'getUnreadChat';
            }

            if(!loadChatBox && chatModalIsVisible())
            {

                var dataObj = {
                sender: contactUserName,
                receiver: chatUserName,
                method: method
                }

                //console.log('get data -->' + JSON.stringify(dataObj));

                ajaxRecipe(dataObj).done(function(data, textStatus, jqXHR)
                    {
                       //console.log(JSON.stringify(data));
                      
                       
                       if (data.chat && !initialChat)
                       {
                            markRedMessageBoxHeader();
                       }

                       confirmedReadData(data);
                       initialChat = false;
                       loadChatBox = true;
                       

                    });

                }

           

        }


        var clearChatBox = function()
        {
            clearInputChatBox();

            if (chatMessageBox.children().length)
            {
                chatMessageBox.empty();
            }


        }                                                     

       
        var sentChatMessage = function(event)
        {
            event.preventDefault();
            event.stopPropagation();
            //turn off input so only once can be clicked
            deActivateInputChat();
             
            var message = chatInputField.val();
            var date = getCurrentDateTime();
            sendMessage = true;

            if (message && sendMessage)
            {

                var dataObj = {
                    sender: chatUserName,
                    receiver: contactUserName,
                    message: message,
                    method: 'saveMessage'
                }

                ajaxRecipe(dataObj).done(function(data, textStatus, jqXHR)
                {
                   if(data.sent)
                   {
                        sendMessage = false;

                        clearInputChatBox();
                        dataObj.send = date;
                        new buildChatBox().displayOneLocalMessage(dataObj); 
                        //turn on input 
                        activateInputChat();
                        
                   }

                });






            }

        }

        var clearInputChatBox = function()
        {
            chatInputField.val('');

        }

        var showInputChat = function()
        {
            if (chatInputFieldFrame.hasClass('hidden'))
            {
                chatInputFieldFrame.removeClass('hidden');

            }

        }

        var triggerSentMessage = function(event)
        {
            if ( event.which == 13 )
            {
                event.preventDefault();
                event.stopPropagation();

                chatSendButton.trigger('click');
            }

        }

        var deActivateInputChat = function()
        {
            chatSendButton.unbind('click', sentChatMessage);
            chatInputField.unbind('keypress', triggerSentMessage);

        }

        var activateInputChat = function()
        {
            chatSendButton.bind('click', sentChatMessage);
            chatInputField.bind('keypress', triggerSentMessage);
        }

        this.initializeDeActivateInputChat = function()
        {
            deActivateInputChat(); //call function externally fon unbind for next user
            loadChatBox = true;    //stop async refreshing chatbox
        }

        this.initializeChatWithUser = function(localUser, guestUser)
        {      
            chatUserName = localUser
            contactUserName = guestUser;

           
            initialChat = true;
            clearChatBox();
            showInputChat();
            //in case this is second chat not the first
            //nut this not workoo as for preciots object is not unbidded 
            //so we have to move this outside as this now work as should
            //so we call the fucnion externslly one moretime while changing user
            //deActivateInputChat();
            //so decativation will not help here
            activateInputChat();
            getChatMessage();

        }
    }