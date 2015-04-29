(function ($, STUDIP) {
    STUDIP.Sockets = {};
    STUDIP.Sockets.server = 'ws://127.0.0.1:8080';

    STUDIP.Sockets.chatInputHandler = function (event) {
        var message = $(this).val().trim();
        
        if (event.keyCode === 27) {
            $(this).val('');
        }

        if (event.keyCode !== 13 || message.length === 0) {
            return;
        }

        $(this).val('');
    };

    $(document).ready(function () {
        $('[data-chat]').each(function () {
            var realm = $(this).data().realm || 'default',
                input = $(this).data().chat,
                connection;

            $(input).attr('disabled', true);

            connection = new autobahn.Connection({
                url: STUDIP.Sockets.server,
                realm: realm,
                max_retries: 1
            });

            connection.onopen = function (session) {
                $(input).attr('disabled', false).keypress(STUDIP.Sockets.chatInputHandler);

               // 1) subscribe to a topic
               function onevent(args) {
                  console.log("Event:", args[0]);
               }
               session.subscribe('com.myapp.hello', onevent);

               // 2) publish an event
               session.publish('com.myapp.hello', ['Hello, world!']);

               // 3) register a procedure for remoting
               function add2(args) {
                  return args[0] + args[1];
               }
               session.register('com.myapp.add2', add2);

               // 4) call a remote procedure
               session.call('com.myapp.add2', [2, 3]).then(
                  function (res) {
                     console.log("Result:", res);
                  }
               );
            };
            
            connection.onclose = function () {
                console.log('Error', arguments);
            };

            connection.open();
        });
    });
}(jQuery, STUDIP));

