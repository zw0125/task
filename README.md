My Project Setup Instructions

1. Install XAMPP (if you haven't already):
   - Download and install XAMPP: https://www.apachefriends.org/index.html
   - Start Apache and MySQL in the XAMPP control panel.

2. Place the project files:
   - Extract the ZIP file to the 'htdocs' directory in your XAMPP installation.
   - The 'htdocs' directory is typically located in `C:\xampp\htdocs\` on Windows.

3. Import the database:
   - Open phpMyAdmin (http://localhost/phpmyadmin).
   - Create a new database (e.g., `my_project`).
   - Select the database, then click on the "Import" tab.
   - Upload the `database_dump.sql` file and click "Go" to import the database.

4. Configure your database connection:
   - Open `db_config.php` and ensure that the database credentials (username, password, database name) match your MySQL setup.
   - By default, XAMPP uses the following credentials:
     - Username: `root`
     - Password: (empty, i.e., no password)
     - Database: `my_project` (or whatever name you gave to your database)

5. Run the project:
   - Open your browser and go to `http://localhost/my_project/register.php` to view the project.

Enjoy using the application!
