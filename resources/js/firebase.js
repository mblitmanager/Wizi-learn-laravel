// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyAAAaZVClNlMXgTktyjUg8lhLG5zSue4YY",
  authDomain: "wizi-learn.firebaseapp.com",
  projectId: "wizi-learn",
  storageBucket: "wizi-learn.firebasestorage.app",
  messagingSenderId: "69521612278",
  appId: "1:69521612278:web:94878d39de047f667c7bd7",
  measurementId: "G-01Y7WC8383"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

export { app, analytics };
