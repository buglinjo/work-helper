var reg;
var sub;

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').then(function() {
        return navigator.serviceWorker.ready;
    }).then(function(serviceWorkerRegistration) {
        reg = serviceWorkerRegistration;
        console.log('Service Worker is ready :^)', reg);
    }).catch(function(error) {
        console.log('Service Worker Error :^(', error);
    });
}

$('document').ready(function () {
    subscribe();
});

function subscribe() {
    reg.pushManager.subscribe({userVisibleOnly: true}).
    then(function(pushSubscription){
        sub = pushSubscription;
        console.log('Subscribed! Endpoint:', sub.endpoint);
    });
}

function unsubscribe() {
    sub.unsubscribe().then(function(event) {
        console.log('Unsubscribed!', event);
    }).catch(function(error) {
        console.log('Error unsubscribing', error);
    });
}