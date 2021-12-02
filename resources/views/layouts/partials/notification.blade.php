@if (setting('vapidKey', '') != '')
    <script src="https://www.gstatic.com/firebasejs/5.9.4/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.9.4/firebase-messaging.js"></script>

    <script>
        var firebaseConfig = {
            messagingSenderId: "{{ setting('messagingSenderId') }}",
        };



        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        initFirebaseMessagingRegistration();

        function initFirebaseMessagingRegistration() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken({
                        vapidKey: "{{ setting('vapidKey') }}"
                    })
                })
                .then(function(token) {
                    console.log(token);
                    //
                    livewire.emit('changeFCMToken', token);

                }).catch(function(err) {
                    console.log('User Token Error' + err);
                });
        }


        messaging.onMessage(function(payload) {
            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(noteTitle, noteOptions);
        });

    </script>
@endif
