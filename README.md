This is the Fork CLI Tool. This tool is build to make life of the developers easier.

To make this usable in your terminal, add export PATH=$PATH:/Path/To/Knife/
You'll now be able to call the knife tool via 'ft'

More documentation can be found here:
https://github.com/siphoc/knife/wiki/_pages

@author	Jelmer Snoeck <jelmer.snoeck@netlash.com>

--------------------------------------
Show information

One of the functionalities of the Fork tool is to show information about your current project.

Currently, these options are available:
show version      This will show the current Fork Version of your project.
show modules      This will show all the modules in your project and if they are installed or not.

-------------------------------------
Possible issues:

------PDO ERROR-----
At the moment, I'm using plain PDO, in the future this tool will connect with Spoon. One of the errors you could
come across is that your mysql socket isn't available. 

The error will be recognizable by:
SQLSTATE[HY000] [2002] No such file or directory

If you come accross this problem, don't worry, don't panic. Just breath.

Now, as a real ninja go to terminal(where your're obviously already in) and type vi /etc/php.ini (or wherever your php ini is)

search the line pdo_mysql.default_socket=/a/path/to/a/mysql.sock

Now, obviously, this is the wrong path. You have 3 choices, set the path to /tmp/mysql.sock or /your/mamp/folder/mysql.sock
If this doesn't work, my last chance of saving you is this:

cd /
find . -name 'mysql.sock'

This could take a while, but at the end it will show you where your mysql.sock file is. Set your path to that file
and hope for the best.
