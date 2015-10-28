 

    var ajaxRecipe = function(inputAjaxObject)
    {
        return $.ajax({

                    cache: false,
       
                    url: "api.php",
              
                    data: {
                       getAjaxData: JSON.stringify(inputAjaxObject)
                    },
                   
                    type: "POST",
                    dataType: "JSON"
               

                }).fail( function( jqXHR, status, errorThrown )
                {
                  
                        console.log( "Error: " + errorThrown );
                        console.log( "Status: " + status );
                        console.dir( jqXHR );
                });

    } 


    var getCurrentDateTime = function()
    {
        var now = new Date();
        var todayPart = ['getFullYear', 'getMonth', 'getDate', 'getHours', 'getMinutes', 'getSeconds'];

        var formatResponse = ''; 
        var responsePart = '';
        var prefix = '';

        for (index in todayPart)
        {
            responsePart = now[todayPart[index]]();
            if (todayPart[index] == 'getMonth')
            {
                responsePart++;
            }

            if (responsePart < 10)
            {
                responsePart = '0' + responsePart;
            }
           

            if (index == 0)
            {
                prefix = '';
            }

            else if (index < 3)
            {
                prefix = '-';
            }
            else if (index == 3)
            {
                prefix = ' ';
            }
            else 
            {
                prefix = ':'
            }
            
            

            
            formatResponse = formatResponse + prefix + responsePart
  

        }

        return formatResponse;
    }


    var buildUserList = function(callback)
    {
        var userListFrameContent = $('#chatModal').find('.chat').eq(1);

        var buildUserLine = function(name, logged, pending) {
            return '<li class="right clearfix">' + 
                        '<div>' +
                            '<span class="glyphicon glyphicon-comment ' + ((pending)?'red-color':'') + '"></span>' +
                            '<span class="log_info ' + ((!logged)?'user_inactive':'') + '" >' + '<i>logged '+ ((!logged)?'out':'in') + '</i>' + '<span>' + 
                                '</div>' + 
                            '<div class="shift-right ' + ((!logged)?'user_inactive':'') + '">' +
                            '<strong class="primary-font">' + name + '</strong>' +
                        '</div>' +
                    '</li>';

        } 

        var clearUserListFrameContent = function()
        {
            
            userListFrameContent.empty();

        }

        var createUserList = function(userList)
        {
            clearUserListFrameContent();

            //build user list for all users and their info
            for (user in userList)
            {
                userListFrameContent.prepend(buildUserLine(user, userList[user].logged, userList[user].pending));
            }

            callback();

        }


        this.initialize = function(userList)
        {
            createUserList(userList);

        } 
    }



    var buildChatBox = function()
    {
        //as jquery find -> eq(0) == find we can define in many ways
        //var chatBoxElement =  $('#chatModal').find('.chat');
        //var chatBoxElement =  $('#chatModal').find('.chat').eq(0);

        var chatFrameElement = $('#chatModal').find('.panel-body').eq(0);
        var chatBoxElement = chatFrameElement.find('.chat').eq(0);


        var scrollDown = function()
        {
            
            chatFrameElement.animate({ 
                        scrollTop: chatBoxElement.height()
                    }, 1*1000);
        }
        
        var buildLocalUserLine = function(message) {
        
            return '<li class="right clearfix">' +
                '<span class="chat-img pull-right">' + 
                    '<img src="img/localUser.png" alt="Me" class="img-circle" />' +
                '</span>' +
                '<div class="chat-body clearfix">' +
                    '<div class="header">' + 
                        '<small class=" text-muted"><span class="glyphicon glyphicon-time"></span>' + message.send + '</small>' +
                            '<strong class="pull-right primary-font">' + message.sender + '</strong>' +
                        '</div>' +
                        '<p class="pull-right">' + message.message + '</p>' +       
                    '</div>' +
                '</li>';




        } 

        var buildRemoteUserLine = function(message) {

            return '<li class="left clearfix">' +
                '<span class="chat-img pull-left">' + 
                    '<img src="img/remoteUser.png" alt="U" class="img-circle" />' + 
                '</span>' + 
                '<div class="chat-body clearfix">' + 
                    '<div class="header">' + 
                        '<strong class="primary-font">"' + message.sender + '"</strong> <small class="pull-right text-muted">' + 
                        '<span class="glyphicon glyphicon-time"></span>' + message.send + '</small>' + 
                    '</div>' + 
                    '<p>' + message.message +'</p>' +
                '</div>' + 
            '</li>' ;

        } 

        var createChatBox = function(messageList, localUser, remoteUser)


        {
             
            //display mesages in chat box;
            var buildFunction = null; 
            var messages = messageList.chat;

            for (message in messages)
            {
               if (messages[message].receiver == localUser)
               {
                  buildFunction = buildRemoteUserLine;
               }
               else if (messages[message].receiver == remoteUser)
               {
                    buildFunction = buildLocalUserLine;
                  
               }

               chatBoxElement.append(buildFunction(messages[message]));

            }

            if(messages)

             scrollDown();


        }


        this.displayOneLocalMessage = function(message)
        {
            
            chatBoxElement.append(buildLocalUserLine(message));

            scrollDown();

        }


        this.displayMessagesList = function(messageList, localUser, remoteUser)
        {
            createChatBox(messageList, localUser, remoteUser);

        } 
    }


    var chatModalIsVisible = function()
        {
            return ($('#chatModal').css('display') != 'none');
        }







