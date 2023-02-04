// Your web app's Firebase configuration
var firebaseConfig = {
    apiKey: "AIzaSyDPcBh3cpryOZ4e2Y_TLkmP1IzXAYwpIuU",
    authDomain: "netframe-d3146.firebaseapp.com",
    databaseURL: "https://netframe-d3146.firebaseio.com",
    projectId: "netframe-d3146",
    storageBucket: "netframe-d3146.appspot.com",
    messagingSenderId: "659315797153",
    appId: "1:659315797153:web:bc88ecd0dff69515c694e0"
  };
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();
console.log(messaging);

async function requestPermission(){
    try {
        messaging.requestPermission();
        const token = messaging.getToken();
        console.log('token do usuário:', token);

        return token;
    }
    catch (error) {
        console.error(error);
    }
}
requestPermission();