Homey
=====
Home Automation Project

This project was build to offer remote management of home appliances using a raspberry PI and X10 Protocol
The main reason of this project is to get familiar with the PI (after years of programming in automotive business -  it's 
rocket science :)

LICENSING:
You should comply with the GPLv3 licensing.

Harware Requisites:
- Raspberry PI
- Piface board (used as IO to allow in-location management)
- Adafruit LCD shield (http://www.adafruit.com/products/714) , but only the blue-white version, not the RGB
- Marmitek CM11A (or CM15Pro) X10 <-> serial transceiver   
- Marmitek AM12 remote controlled relay  
- USB <-> Serial Adapter if  CM11A is used instead of CM15Pro. The latter one has already built-in USB <-> Serial interface 

Note:
- The Piface board was used as a first version, where the 8 leds were indicating the status of the first 8 configured devices.
The menu selection works by using the 5 buttons located on the board. 
WiringPi library was used to provide SW PWM solution to blink the leds (when using the menu selection)
- The Second version will use both Piface and LCD shields (selectable via command line switch). This will be handled by the LCDd daemon. 
- 

Software Requisites:
- Raspberry Pi with a debian release (Raspbian, Rasbmc)
- heyu
- lighthttpd+php+mysql
- wiringpi (for the pifaceboard)


SYSTEM DESCRIPTION:
The CM15Pro transceiver provides the interface between the power line and the raspberry. It is recognized as an virtual USB port (/dev/ttyUSB0 - usually). The heyu command line SW implements the S10 protocol. This is a very complex protocol, 
but in this project, only a limited command set is being used: ON and OFF. The goal is to be able to turn on or off any device that is connected to the AM12 relays. 

On SW side there a an:
- resident C application
- php script running on a webserver (lighthttpd)
The role of the resident C application is to monitor the I/O buttons (on the Piface or on the LCD shield) and provide a menu system that allows any preconfigured (in mysql database) device to be turned on or off. It will also update the database with the latest status of the devices (the AM12 devices have only "one-way" communication possibilities, therefore it's not possible the read back the actual status).  It will also store the current time of the last modification. It will not keep a journal about the modifications, only the state of the last one.
It will also drive the leds on the PIface board to reflect the actual status of the AM12 relay sockets. In "Menu mode", the user can navigate through the selected devices (the leds will flash - using wiringpi's sw PWM driver) and once a selection is made, all of the leds will light up as a confirmation.  Also, there is a status led that will flash slowly (just like the lighthouse in the Great Gatsby movie) to indicate that the process is running.

For the lcd shield, the goal is to display the currently selected device (it's name and id) and it's status, and, using the buttons from the panel, to be able to switch it off or on).

Also, the resident C application monitors a shared memory location between the lighthttpd webserver. This is used to update the status only if somebody updated the status via the web interface. 
It's purpose is to minimize the CPU load, as otherwise a more frequent mysql query would have been necessary to keep in sync the display and the database.
Regardless of this, there is still a cyclic update of 1minute (#defined)

The state of the AM12 relays can be also changed from a basic web interface. This is assured by a small php scipt that queries the mysql database, displays the currently configured devices, their status and the last modification and allows it to be changed. Also, in case the status is changed, it will update the database accordingly and notify the C app via the shared memory.


INSTALLATION PREREQUISITES:
- Rasbmc:
Installing raspbian or xbmc is covered in several forums.
- Heyu:
This command line utility is responsible of implementing the X10 protocol. This utility will be called by the web or the linux application when it needs to change the state of the relays.
Installation instructions here:
http://x10linux.blogspot.hu/2012/08/installing-heyu-on-raspberry-pi.html
- Lighthttpd+php+mysql:
I've chosen a more lightweight server instead of apache. It has php/cgi addon and also mysql support
Install instructions here:
http://www.everydaylinuxuser.com/2013/06/setting-up-personal-web-server-on.html
PhpMyadmin will come handy when handling the databases.
- Wiringpi:
Special thanks to the creator of this library, it's a big help to connect the "outside world" to the PI. 
It's continuously developed and more and more addon boards are supported.
Install instructions here:
http://wiringpi.com/
- Mysql developement headers, standard C developement headers.  Google will help you how to install all these.


BUILDING THE C APPLICATION:
There is a makefile written , so a "make homey" should do the trick. The code is written in plain C, compiles using the GNU C compiler, but beware, it  uses semaphores, shared memories, mysql headers, and these might require additional devel-packages.

Once the application is build, it can be installed anywhere. There is also an init script provided, where you can specify using command line options the connection details. A future developement idea would be to store this in a standard config file.


CONFIGURING THE LCD PROC DAEMON:
There is already an LCD proc configuration file included in the project, for more info the following link oculd be useful:
http://www.raspberrypi.org/forums/viewtopic.php?f=91&t=34039

Please note, that there is no need to patch the source of lcdproc any more, just use the settings suggested there.



More documentation will come.



