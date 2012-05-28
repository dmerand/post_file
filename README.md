post_file
=========

Author: [Donald L. Merand](http://donaldmerand.com)

Your own personal embed server. Allows you to quickly upload a file to your webserver and get a URL back. I use it for writing blog posts.


Usage
=====

- Put the entire `public/` directory somewhere on your server.
- Then, use the `post_file.sh` script like this:
    - Modify the script to point to your server, with your username etc, and your security token (see below).
    - Type `sh post_file.sh file_name` in [your favorite terminal program](http://www.iterm2.com/).
    - You should get a link like `http://your.site/1338166636/your_file`
        - The directory before your file is the UNIX timestamp of when you uploaded the file. The thinking is that you might want to upload the same file twice, so you want each file to be located in its own unique directory.
    - Take that link and paste it wherever you need it.
    - If you need/want to delete that uploaded file for whatever reason, use `sh post_file.sh -d 1338166636`. More on this below.
- The ability to delete files via the "api" becomes necessary on shared hosts (such as mine) where the web user and the SSH user don't have the same privileges. You upload a file, but then you can't delete it when you're logged on to the webserver since the 'www' user (or some such) is the owner! To get around this, I modified the PHP script to accept HTTP DELETE requests as well. That's like web 3.0 right there. You still need a token though.
- Security is handled two different ways. One, there is an API "token" that you need for the PHP script to do anything. I recommend changing the default. Two, I've enabled HTTP Basic Authentication and built that into the scripts. So, when cloning the files be sure to change both the API token, and to add a `.htaccess` and `.passwd` file to the `public` directory if you want to use HTTP authentication (I've omitted mine from the repository to make it four seconds harder for you to figure out the information contained in them).


License
=======
MIT-Licensed.
