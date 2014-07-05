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


INSTALLATION:
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
