#Knife
This is the Fork CLI Tool. This tool is build to make life of the developers easier.

To make this usable in your terminal, add 'alias ft=/Path/To/Knife' in your .bashrc
You'll now be able to call the knife tool via 'ft'

##Setting up
Don't forget to setup your knife, otherwise it'll use my credits in your projects. The .ftconfig file is meant as an example.

##Documentation
At the moment, the inline help function is not very helpfull yet, this is a work in progress.

I've posted a [blogpost](http://siphoc.com/news/detail/knife-the-fork-cli-tool) about this on my website. It contains a small tutorial on how to use the Fork CLI Tool.

There's also a [wiki on github](https://github.com/siphoc/knife/wiki/_pages)

##Possible issues

### PDO Error
At the moment, I'm using plain PDO, in the future this tool will connect with Spoon. One of the errors you could
come across is that your mysql socket isn't available.

The error will be recognizable by:
SQLSTATE[HY000] [2002] No such file or directory

If you come accross this problem, don't worry, don't panic. Just breath.

Now, as a real ninja go to terminal(where your're obviously already in) and type vi /etc/php.ini (or wherever your php ini is, just make sure it is the one the command line uses. To check this you could type php --ini)

search the line pdo_mysql.default_socket=/a/path/to/a/mysql.sock

Now, obviously, this is the wrong path. You have 3 choices, set the path to /tmp/mysql.sock or /your/mamp/folder/mysql.sock
If this doesn't work, my last chance of saving you is this:

cd /
find . -name 'mysql.sock'

This could take a while, but at the end it will show you where your mysql.sock file is. Set your path to that file
and hope for the best.
