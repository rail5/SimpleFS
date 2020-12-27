# SimpleFS
Simple, Self-Hosted, PHP File Sharing

# Features
- Portable
- Dual- or single-user set up *(users with permission to upload)*
- Flatfile

### Usage
- Place files on your web server *(doesn't have to be in the root directory)*
- Visit **setup.php** in your web browser to create a user or two to upload files
- Delete **setup.php** after setting up, as leaving it there is a security risk

*That's it*

### User Recovery / Forgot Password
- If you forget your password, delete the *contents* of **config.global.php** and download & re-run **setup.php**

# Requirements
* [PHP 7.2+](https://www.php.net)

- Please ensure that your **php.ini** *permits uploads.*
Check for the line:
```
file_uploads = On
```

# Screenshots
![Sign in](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-login.png)
![Upload](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-upload.png)
![Download](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-download.png)
![Manage](https://raw.githubusercontent.com/rail5/SimpleFS/main/screen-manage.png)
