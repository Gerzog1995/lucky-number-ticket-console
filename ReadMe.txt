---------------------------------------------------------
Step #1
Docker.

Run console in \. directory
# docker build -t parser .
# docker run -v {LOCAL_LINK}/:\var\www\html -p 33:80 parser

---------------------------------------------------------
Step #2
Console (on virtual-host).

Run console in \. directory
# php index.php abs