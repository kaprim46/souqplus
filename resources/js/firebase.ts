import { initializeApp } from 'firebase/app';
import { getDatabase } from 'firebase/database';


const firebaseConfig = {
    apiKey: "AIzaSyBi-NId_JBbPHHHoiEJG8eiKOYIGctylFg",
    authDomain: "sooqplus-c102a.firebaseapp.com",
    databaseURL: "https://sooqplus-c102a-default-rtdb.firebaseio.com",
    projectId: "sooqplus-c102a",
    storageBucket: "sooqplus-c102a.appspot.com",
    messagingSenderId: "753077715460",
    appId: "1:753077715460:web:c71095ce0b775f6cfe2b51",
    measurementId: "G-0BPXKVYD6F"
};

const app = initializeApp(firebaseConfig);

const database = getDatabase(app);

export { database };