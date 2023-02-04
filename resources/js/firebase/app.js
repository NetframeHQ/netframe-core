import firebase from 'firebase/app';
import 'firebase/app';
import 'firebase/messaging';
import FingerprintJS from '@fingerprintjs/fingerprintjs';

var config = {
    apiKey: "AIzaSyDPcBh3cpryOZ4e2Y_TLkmP1IzXAYwpIuU",
    authDomain: "netframe-d3146.firebaseapp.com",
    databaseURL: "https://netframe-d3146.firebaseio.com",
    projectId: "netframe-d3146",
    storageBucket: "netframe-d3146.appspot.com",
    messagingSenderId: "659315797153",
    appId: "1:659315797153:web:e3a5d38ed8db0186c694e0"
};

firebase.initializeApp(config);

const messaging = firebase.messaging();
messaging.requestPermission().then(function() {
    return messaging.getToken();
}).then(function(token){

    // generate browser uniq id with lib @fingerprintjs/fingerprintjs
    FingerprintJS.load().then(fp => {
        fp.get().then(result => {
            // This is the visitor identifier:
            const browserId = result.visitorId;

            // send browser id and fcm token to back end
            var fcmDeviceUrl = laroute.route('app.device.fcm', {duuid: browserId, fcmToken: token, deviceType: 'browser'});
            var xhr = new XMLHttpRequest();
            xhr.open('GET', fcmDeviceUrl, true);
            xhr.send(null);
        });
    });
}).catch(function(err) {
    console.log('Permission denied', err);
});

messaging.onMessage(function(payload){
    //console.log('onMessage: ',payload);
});