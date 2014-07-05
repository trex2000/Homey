################################################################################
# Automatically-generated file. Do not edit!
################################################################################

# Add inputs and outputs from these tool invocations to the build variables 
C_SRCS += \
R:/home/pi/x10/main.c 

OBJS += \
./main.o 

C_DEPS += \
./main.d 


# Each subdirectory must supply rules for building sources it contributes
main.o: R:/home/pi/x10/main.c
	@echo 'Building file: $<'
	@echo 'Invoking: Cross GCC Compiler'
	gcc -I"r:\usr\include\" -O3 -Wall -c -fmessage-length=0 -MMD -MP -MF"main.d" -MT"main.d" -o "$@" "$<"
	@echo 'Finished building: $<'
	@echo ' '


