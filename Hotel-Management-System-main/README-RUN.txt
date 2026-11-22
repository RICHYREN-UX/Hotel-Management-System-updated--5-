HOW TO RUN - Sport's Man Arms Hotel Management System
=====================================================

OPTION 1 - run.bat (easiest)
  1. Install XAMPP (https://www.apachefriends.org) in C:\xampp
  2. Right-click the zip > Extract All
  3. Open the extracted folder and double-click run.bat
  4. Login:  Admin@gmail.com  /  1234
  (Do NOT run it from inside the zip.)

OPTION 2 - XAMPP Control Panel (if run.bat fails)
  1. Open XAMPP Control Panel, Start "Apache" and "MySQL".
  2. Copy this folder (Hotel-Management-System-main) into C:\xampp\htdocs
  3. Open http://localhost/phpmyadmin
  4. Click Import, choose sportsmanarms.sql, click Import.
     (The file creates the database itself. It also ERASES an existing
      sportsmanarms database, so only do this the first time.)
  5. Open http://localhost/Hotel-Management-System-main/
  6. Login:  Admin@gmail.com  /  1234

PAYMENTS
  Payment page and Room Booking page have a green "Paid" button.
  Click it, choose Cash / M-Pesa / Card / Bank Transfer, add a reference,
  and confirm. The method shows on the Payment page and on the invoice.
