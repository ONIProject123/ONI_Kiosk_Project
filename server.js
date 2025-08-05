const express = require('express');
const mysql = require('mysql2');
const bodyParser = require('body-parser');
const path = require('path');

const app = express();
const PORT = 3000;

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(express.static('public'));

// Database connection pool for better connection management
const dbConfig = {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'info',
    acquireTimeout: 60000,
    timeout: 60000,
    reconnect: true,
    connectionLimit: 10,
    queueLimit: 0,
    timezone: 'local', // Use local timezone to prevent date shifting
    dateStrings: true  // Return dates as strings instead of Date objects
};

// Create connection pool
const pool = mysql.createPool(dbConfig);

// Test database connection
pool.getConnection((err, connection) => {
    if (err) {
        if (err.code === 'ECONNREFUSED') {
            console.error('❌ MySQL server is not running!');
            console.error('Please start XAMPP and ensure MySQL service is running on port 3306');
            console.error('Then restart this application');
        } else {
            console.error('Database connection failed:', err);
        }
        return;
    }
    console.log('Connected to MySQL database successfully!');
    connection.release();
});

// Handle connection errors
pool.on('connection', function (connection) {
    console.log('DB Connection established as id ' + connection.threadId);
});

pool.on('error', function(err) {
    console.error('Database error:', err);
    if(err.code === 'PROTOCOL_CONNECTION_LOST') {
        console.log('Database connection was closed.');
    }
    if(err.code === 'ER_CON_COUNT_ERROR') {
        console.log('Database has too many connections.');
    }
    if(err.code === 'ECONNREFUSED') {
        console.log('Database connection was refused.');
    }
});

// Serve the main page
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Handle search requests
app.post('/search', (req, res) => {
    const { patientId, firstName, middleName, lastName, suffix, birthdate } = req.body;
    
    // Check if database is available before attempting query
    pool.getConnection((connErr, testConnection) => {
        if (connErr) {
            if (connErr.code === 'ECONNREFUSED') {
                return res.json({ 
                    success: false, 
                    error: 'Database server is not running. Please start XAMPP MySQL service.' 
                });
            }
            return res.json({ 
                success: false, 
                error: 'Database connection error: ' + connErr.message 
            });
        }
        testConnection.release();
        
        // Proceed with search query
        performSearch();
    });
    
    function performSearch() {
    // Build the search query (using your exact column names)
    let query = "SELECT * FROM patients WHERE 1=1";
    const params = [];
    
    // Add conditions based on provided data
    if (patientId && patientId.trim()) {
        query += " AND `Patient ID` = ?";
        params.push(patientId.trim());
    }
    
    // Case-insensitive search for names
    if (firstName && firstName.trim()) {
        query += " AND LOWER(`First Name`) LIKE LOWER(?)";
        params.push(`%${firstName.trim()}%`);
    }
    
    if (middleName && middleName.trim()) {
        query += " AND LOWER(`Middle Name`) LIKE LOWER(?)";
        params.push(`%${middleName.trim()}%`);
    }
    
    if (lastName && lastName.trim()) {
        query += " AND LOWER(`Last Name`) LIKE LOWER(?)";
        params.push(`%${lastName.trim()}%`);
    }
    
    // Date handling - expects yyyy-mm-dd format from date input
    if (birthdate && birthdate.trim()) {
        query += " AND `Birthdate` = ?";
        params.push(birthdate.trim());
    }
    
    // Execute the search query using connection pool
    pool.query(query, params, (err, results) => {
        if (err) {
            console.error('Database query error:', err);
            
            // Handle specific error types
            if (err.code === 'ECONNREFUSED') {
                return res.json({ 
                    success: false, 
                    error: 'Database server is not running. Please start XAMPP MySQL service.' 
                });
            } else if (err.code === 'PROTOCOL_CONNECTION_LOST' || err.code === 'ECONNRESET') {
                return res.json({ 
                    success: false, 
                    error: 'Database connection lost. Please try again.' 
                });
            }
            
            return res.json({ 
                success: false, 
                error: 'Database error: ' + err.message 
            });
        }
        
        if (results.length > 0) {
            res.json({ 
                success: true, 
                records: results,
                count: results.length 
            });
        } else {
            res.json({ 
                success: false, 
                error: 'NO RECORD FOUND!' 
            });
        }
    });
    }
});

// Graceful shutdown
process.on('SIGINT', () => {
    console.log('Shutting down gracefully...');
    pool.end(() => {
        console.log('Database pool closed.');
        process.exit(0);
    });
});

process.on('SIGTERM', () => {
    console.log('Shutting down gracefully...');
    pool.end(() => {
        console.log('Database pool closed.');
        process.exit(0);
    });
});

// Start server
app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
    console.log('Make sure XAMPP MySQL is running!');
});