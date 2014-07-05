@echo off
cls
SET PATH_TO_OUTPUT_FILE=/home/pi/x10/homey
REM PATH TO PLINK EXECUTABLE
SET PLINK_EXEC=plink.exe
SET USERNAME=root
SET PASSWORD=ladyhawke
SET IP_ADDR=192.168.137.8
SET PORT=10000
REM END OF CONFIGURATION
REM ====================
SET LAUNCH_PARAMS=gdbserver %IP_ADDR%:%PORT% %PATH_TO_OUTPUT_FILE% %&
echo Killing previous gdbserver
%PLINK_EXEC% %USERNAME%@%IP_ADDR% -pw %PASSWORD% killall gdbserver
@echo Launching GDBServer
%PLINK_EXEC% %USERNAME%@%IP_ADDR% -pw %PASSWORD% %LAUNCH_PARAMS%
@echo GDBServer Launch Completed. It will be stopped by debugger once debugging is finished.