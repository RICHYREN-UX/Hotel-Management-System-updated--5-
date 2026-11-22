@echo off
title Sport's Man Arms Hotel Management System
cd /d "%~dp0"
echo ================================================
echo  Sport's Man Arms Hotel Management System
echo ================================================
echo.

set "DBNAME=sportsmanarms"
set "SQLFILE=%~dp0sportsmanarms.sql"
set "PORT=8000"

REM ---- 1. must not be running from inside a zip ----
echo %~dp0 | find /i ".zip\" >nul
if not errorlevel 1 goto inzip

REM ---- 2. find XAMPP ----
set "XAMPP="
if exist "C:\xampp\php\php.exe" set "XAMPP=C:\xampp"
if not defined XAMPP if exist "D:\xampp\php\php.exe" set "XAMPP=D:\xampp"
if not defined XAMPP if exist "E:\xampp\php\php.exe" set "XAMPP=E:\xampp"
if not defined XAMPP if exist "F:\xampp\php\php.exe" set "XAMPP=F:\xampp"
if not defined XAMPP if exist "C:\Program Files\xampp\php\php.exe" set "XAMPP=C:\Program Files\xampp"
if not defined XAMPP if exist "C:\Program Files (x86)\xampp\php\php.exe" set "XAMPP=C:\Program Files (x86)\xampp"
if not defined XAMPP goto noxampp
echo [1/4] XAMPP found: %XAMPP%

set "PHP=%XAMPP%\php\php.exe"
set "MYSQLD=%XAMPP%\mysql\bin\mysqld.exe"
set "MYSQL=%XAMPP%\mysql\bin\mysql.exe"
set "MYSQLADMIN=%XAMPP%\mysql\bin\mysqladmin.exe"

REM ---- 3. start MySQL if it is not running ----
"%MYSQLADMIN%" -u root ping >nul 2>&1
if not errorlevel 1 goto mysqlok
echo [2/4] Starting MySQL, please wait...
start "" /min "%MYSQLD%" --defaults-file="%XAMPP%\mysql\bin\my.ini" --standalone
set /a tries=0
:waitmysql
timeout /t 1 /nobreak >nul
"%MYSQLADMIN%" -u root ping >nul 2>&1
if not errorlevel 1 goto mysqlok
set /a tries+=1
if %tries% lss 40 goto waitmysql
goto mysqlfail
:mysqlok
echo [2/4] MySQL is running.

REM ---- 4. import the database only the first time ----
"%MYSQL%" -u root -e "USE %DBNAME%" >nul 2>&1
if not errorlevel 1 goto dbok
echo [3/4] Creating database for the first time...
"%MYSQL%" -u root < "%SQLFILE%"
if errorlevel 1 goto dbfail
echo [3/4] Database created.
goto dbdone
:dbok
echo [3/4] Database found - your data is kept.
:dbdone

REM ---- 5. pick a free port starting at 8000 ----
:findport
netstat -an | find ":%PORT% " | find "LISTENING" >nul
if errorlevel 1 goto portfree
set /a PORT+=1
if %PORT% lss 8020 goto findport
:portfree

echo [4/4] Starting web server on port %PORT% ...
start "Hotel Web Server - close this window to stop" /d "%~dp0" "%PHP%" -S localhost:%PORT% -t "%~dp0"
timeout /t 3 /nobreak >nul
start "" "http://localhost:%PORT%/index.php"

echo.
echo ================================================
echo  RUNNING:  http://localhost:%PORT%
echo  Login:    Admin@gmail.com   /   1234
echo  To stop:  close the "Hotel Web Server" window
echo ================================================
echo.
pause
exit /b 0

:inzip
echo [ERROR] You are running this from inside the ZIP file.
echo.
echo Right-click the zip, choose "Extract All...", open the extracted
echo folder and run run.bat from there.
echo.
pause
exit /b 1

:noxampp
echo [ERROR] XAMPP was not found.
echo.
echo Install XAMPP from https://www.apachefriends.org (default folder C:\xampp)
echo and run this file again. Or follow README-RUN.txt.
echo.
pause
exit /b 1

:mysqlfail
echo [ERROR] MySQL did not start.
echo.
echo Open "XAMPP Control Panel", click Start next to MySQL, then run this again.
echo If it still fails, another MySQL may be using port 3306.
echo.
pause
exit /b 1

:dbfail
echo [ERROR] The database could not be created.
echo.
echo Use phpMyAdmin instead - see README-RUN.txt.
echo.
pause
exit /b 1
