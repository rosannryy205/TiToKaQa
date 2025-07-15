// src/firebase.js
import { initializeApp } from "firebase/app";
import { getDatabase } from "firebase/database";

// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyA3vvC2F2uepQA0B0bsWTJy3Y7FJs9-9bU",
  authDomain: "delivery-tracking-60ca4.firebaseapp.com",
  databaseURL: "https://delivery-tracking-60ca4-default-rtdb.firebaseio.com",
  projectId: "delivery-tracking-60ca4",
  storageBucket: "delivery-tracking-60ca4.firebasestorage.app",
  messagingSenderId: "127674850688",
  appId: "1:127674850688:web:a9940dc3b60182f9c37565",
  measurementId: "G-KHGN5V63HL"
};

const app = initializeApp(firebaseConfig);
const database = getDatabase(app);

export { database };
