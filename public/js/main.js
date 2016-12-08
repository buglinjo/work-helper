var reg;
var sub;

if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('sw.js')
        .then(function (swReg) {
            reg = swReg;
            subscribe(reg);
            swRegistration = swReg;
        })
        .catch(function (error) {
            console.error('Service Worker Error', error);
        });
} else {
    console.warn('Push messaging is not supported');
}

$(document).ready(function () {
    getData();
});

function subscribe(reg) {
    reg.pushManager.getSubscription()
        .then(function (subscription) {
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
    sub.unsubscribe().then(function (event) {
        console.log('Unsubscribed!', event);
    }).catch(function (error) {
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
    }).fail(function () {
        unsubscribe();
    });
}

function getData() {
    $.ajax({
        url: "/get-data",
        method: "post",
        data: {
            _token: $('#_token').val()
        }
    }).done(function (data) {
        updateData(data);
    }).always(function () {
        setTimeout(getData, 3000);
    });
}


function updateData(data) {
    $('#name').text(data.name);
    $('#today').text(data.today);
    $('#salary').text(data.salary);
    $('#daysPassedAfterSalary').text(data.daysPassedAfterSalary);
    $('#isDayNum').text(data.isDayNum);
    $('#daysLeftUntilSalary').text(data.daysLeftUntilSalary);

    if(data.isLunchBreak) {
        $('.isLunchBreak').show();
    } else {
        $('.isLunchBreak').hide();
    }
}