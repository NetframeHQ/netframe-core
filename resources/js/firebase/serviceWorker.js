importScripts('https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.4/firebase-messaging.js');

var firebaseConfig = {
    apiKey: "AIzaSyDPcBh3cpryOZ4e2Y_TLkmP1IzXAYwpIuU",
    authDomain: "netframe-d3146.firebaseapp.com",
    databaseURL: "https://netframe-d3146.firebaseio.com",
    projectId: "netframe-d3146",
    storageBucket: "netframe-d3146.appspot.com",
    messagingSenderId: "659315797153",
    appId: "1:659315797153:web:e3a5d38ed8db0186c694e0"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Messaging
const messaging = firebase.messaging();