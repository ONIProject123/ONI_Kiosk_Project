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

// Database connection (same as your XAMPP setup)
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'info'
});

// Test database connection
db.connect((err) => {
    if (err) {
        console.error('Database connection failed:', err);
        return;
    }
    console.log('Connected to MySQL database successfully!');
});

// Serve the main page
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Handle search requests
app.post('/search', (req, res) => {
    const { patientId, firstName, middleName, lastName, suffix, birthdate } = req.body;
    
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
    
    // Execute the search query
    db.query(query, params, (err, results) => {
        if (err) {
            console.error('Database query error:', err);
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
});

// Start server
app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
    console.log('Make sure XAMPP MySQL is running!');
});