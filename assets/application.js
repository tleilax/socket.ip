(function ($, STUDIP) {
    STUDIP.Sockets = {};
    STUDIP.Sockets.server = 'ws://127.0.0.1:8080';

    $(document).ready(function () {
        $('[data-chat]').each(function () {
            var realm = $(this).data().realm || 'default',
                input = $(this).data().chat,
                output = $(this),
                connection;

            $(input).attr('disabled', true);

            connection = new autobahn.Connection({
                url: STUDIP.Sockets.server,
                realm: realm,
                max_retries: 1
            });

            connection.onopen = function (session) {
                $(input).attr('disabled', false).keypress(function (event) {
                    var message = $(this).val().trim();
        
                    if (event.keyCode === 27) {
                        $(this).val('');
                    }

                    if (event.keyCode !== 13 || message.length === 0) {
                        return;
                    }

                    output.append(message + "\n");
                    session.publish('studip.chat', [message]);

                    $(this).val('');
                });

                session.subscribe('studip.chat', function onevent(args) {
                    output.append(args[0].trim() + "\n");
                });
            };
            
            connection.onclose = function () {
                console.log('Error', arguments);
            };

            connection.open();
        });
    });
}(jQuery, STUDIP));

