var reg;
var sub;

if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('sw.js')
        .then(function(swReg) {
            reg = swReg;
            subscribe(reg);
            swRegistration = swReg;
        })
        .catch(function(error) {
            console.error('Service Worker Error', error);
        });
} else {
    console.warn('Push messaging is not supported');
}

function subscribe(reg) {
    reg.pushManager.getSubscription()
        .then(function(subscription) {
            isSubscribed = !(subscription === null);
            if (!isSubscribed) {
                reg.pushManager.subscribe({userVisibleOnly: true}).then(function (pushSubscription) {
                    sub = pushSubscription;
                    saveEndpoint(sub.endpoint);
                    console.log('Subscribed! Endpoint:', sub.endpoint);
                });
            }
        });
}

function unsubscribe() {
    sub.unsubscribe().then(function(event) {
        console.log('Unsubscribed!', event);
    }).catch(function(error) {
        console.log('Error unsubscribing', error);
    });
}

function saveEndpoint(endpoint) {
    $.ajax({
        url: "/save-endpoint",
        method: "post",
        data: {
            _token: $('#_token').val(),
            endpoint: endpoint
        }
    }).fail(function() {
        unsubscribe();
    });
}