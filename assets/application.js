/*jslint browser: true, devel: true */
/*global jQuery, STUDIP, autobahn */
(function ($, STUDIP, autobahn) {
    'use strict';

    STUDIP.Sockets = {};
    STUDIP.Sockets.server = 'ws://127.0.0.1:8080';

    $(document).ready(function () {
        $('[data-chat]').each(function () {
            var realm = $(this).data().realm || 'default',
                input = $(this).data().chat,
                output = (function (container) {
                    return function (message) {
                        $(container).append(message.trim() + "\n");
                        container.scrollTop = container.scrollHeight;
                    };
                }(this)),
                connection;

            $(input).prop('disabled', true);

            connection = new autobahn.Connection({
                url: STUDIP.Sockets.server,
                realm: realm,
                max_retries: 1
            });

            connection.onopen = function (session) {
                $(input).prop('disabled', false).keypress(function (event) {
                    var message = $(this).val().trim();

                    if (event.keyCode === 27) {
                        $(this).val('');
                    }

                    if (event.keyCode !== 13 || message.length === 0) {
                        return;
                    }

                    output(message);
                    session.publish('studip.chat', [message]);

                    $(this).val('');
                });

                session.subscribe('studip.chat', function onevent(args) {
                    output(args[0]);
                });
            };

            connection.onclose = function (reason, details) {
                if (reason === 'unreachable') {
                    output('unable to connect');
                    return details.will_retry;
                }

                if (reason === 'closed') {
                    $(input).prop('disabled', true).blur();
                    output('server closed the connection');
                    return false;
                }

                if (reason === 'lost' || !details.will_retry) {
                    $(input).prop('disabled', true).blur();
                    output('connection lost');
                    return false;
                }
            };

            connection.open();
        });
    });
}(jQuery, STUDIP, autobahn));

