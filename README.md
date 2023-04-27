# SimpleFS
Simple, Self-Hosted, PHP File Sharing

# Features
- Portable & self-contained
- Option to automatically delete files after a certain length of time
- Automatically ZIPs files if you upload more than one at a time
- SQLite (No need for a bulky SQL Server)
- Dual- or single-user set up *(users with permission to upload)*

### Installation
- Place SimpleFS **anywhere** on your web server
- Visit **setup.php** in your web browser to create a user or two to upload files

*That's it*

# Requirements
* [PHP 7.4+](https://www.php.net)
* [SQLite Module for PHP](https://www.php.net/manual/en/sqlite3.installation.php)
* [ZIP Module for PHP](https://www.php.net/manual/en/zip.installation.php)

- Please ensure that your **php.ini** *permits uploads.*
Check for the line:
```
file_uploads = On
```

# Screenshots
![Main](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-main.png)
![Sign in](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-login.png)
![Upload](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-upload.png)
![Download](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-download.png)
![Manage](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-manage.png)

### Usage

- After signing in, upload files via **upload.php**
- After uploading, shareable links can be obtained from **manage.php** in the format **download.php?id=XXXXX**

### Security
- For Nginx Users

It's recommended that you alter your server configuration to block direct access to the sqlite database file (created by **setup.php** as **filedb.sqlite**), and to block direct access to the *files directory*, as such for example:
```
	location = /SimpleFS/Installation/Folder/filedb.sqlite {
		deny all;
		return 404;
	}
	location = /SimpleFS/Installation/Folder/files/ {
	deny all;
	return 404;
	}
```
- For Apache Users

This repo includes .htaccess files preventing direct access to the sqlite database file and to the files directory. Please ensure that your Apache installation is configured to allow .htaccess overrides, as in for instance, in your **apache2.conf**:
```
<Directory /var/www/>
		Options Indexes FollowSymLinks
		AllowOverride All
		Require all granted
</Directory>
```

These security measures *aren't absolutely essential*, as regardless, nobody can upload files without being signed in. However, the public-facing download links being in the form of **download.php?id=XXXXX** is considered to be a *security feature* as it helps to prevent **unintended recipients** from discovering and downloading files not meant for them. In this same vein, it's a good idea to disallow indiscriminate access to the FileDB and files directory.
