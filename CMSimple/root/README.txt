
===================================================== 
      Help to install CMSimple 5.14 or higher
=====================================================

I N S T A L L A T I O N 
----------------------- 
1. Unzip the zip file in a folder of your PC.
2. Upload all files and folders of the folder, where also this README.txt is located, to your webserver, to the folder, where your CMSimple Website shall run.

Now you can call your new website with a web browser . You can log in for 10 min with the password "test" and change the password.

10 min after the upload, you can no longer log in. You have to run Setup to assign a password.

S E T U P
---------
After the 10 min, or if you have forgotten the password, you also can change the password via setup:

1. Upload the file ./setup/setupControl.php to the installation folder of your CMSimple website (CMSimpleRoot, second language, subsite), and the setup mode will be activated.
2. On some older servers maybe you have to make the setupControl.php writable (666) to activate Setup.
3. Call the ./setup.php (CMSimpleRoot, second language, subsite) with a web browser and set your password.

You have 10 min for the setup, after that you have to upload the file setupControl.php again. Maybe the setupControl.php file need to be opened on the PC, edited and saved to give the file a new edit date. Now the 10 min for setup are running again.

The setup can (must) also be executed in secondary languages and subsites.

After setup, the ./setupControl.php file is automatically deleted and setup is deactivated. 

U P D A T E S
------------- 
The language files will not be updated by updates.

Only the file ./cmsimple/languages/default.php will be updated, newly introduced language variables appear in English in all languages and can be translated in the backend. However, you can also update the language files en.php and de.php manually.

You will find the files en.php and de.php in the folder ./setup/defaults/ of the download, upload the two files to the folder ./cmsimple/languages/ to update the language files manually.

S A F E T Y  N O T I C E 
------------------------
For safety, after setup you should call the ./setup.php file again. If setup is still active, please delete the file ./setupControl.php by ftp.

FORGOTTEN PASSWORD? NO LOGIN POSSIBLE after an update?

Activate the setup and call the setup.php (see S E T U P)

Further informations you get under: https://cmsimple.org/

F I L E-  A N D  F O L D E R  P E R M I S S I O N S
--------------------------------------------------- 
On modern web spaces you should not have to worry about this. However, there are still web servers on which you have to grant write permissions for certain files and folders.

This you can to via ftp. If something like 'Config file missing' appears on the screen after uploading, at first you have to make the following folders writable:

FOLDERS (chmod 0777): 

./backups/
./backups/cmsimple/

./cmsimple/
./cmsimple/languages/

./content/

./templates/
./templates/cmsimple_default/

./Userfiles/
./Userfiles/_core/
./Userfiles/co_author/
./Userfiles/downloads/
./Userfiles/images/
./Userfiles/media/
./Userfiles/plugins/


Then open your website in the browser, your CMSimple website should already work now. Log in and change the password.

It may be that some files now need write permissions, for example:

FILES (chmod 0666):

./cmsimple/config.php
./cmsimple/log.php

./content/_cmsimpleAdmin.php
./content/_disabled_plugins.txt
./content/content.php
./content/pagedata.php


./templates/__cmsimple_default__/stylesheet.css
./templates/__cmsimple_default__/template.htm 

CMSimple should report in the backend when a file needs write permissions.