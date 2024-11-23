import mysql from 'mysql';
import { HOST, USER, PASSWORD, DB, CHARSET } from '../config/db.config.js';

// Create a connection to the database
export const connection = mysql.createConnection({
    host: HOST,
    user: USER,
    password: PASSWORD,
    database: DB,
    charset: CHARSET
});

// open the MySQL connection
connection.connect(error => {
    if (error) throw error;
    console.log("Successfully connected to the database.");
});