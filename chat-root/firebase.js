import admin from 'firebase-admin';
import fs from 'fs/promises'; // Import the promises version of the fs module
const serviceAccountPath = "./firebase-adminsdk.json";
(async () => {
    try {
        const serviceAccountJSON = await fs.readFile(serviceAccountPath, 'utf-8');
        const serviceAccount = JSON.parse(serviceAccountJSON);
        // console.dir(serviceAccount);
        admin.initializeApp({
            credential: admin.credential.cert(serviceAccount)
        });

        // Your push notification sending code here
    } catch (error) {
        console.error('Error:', error);
    }
})();

export { admin };
