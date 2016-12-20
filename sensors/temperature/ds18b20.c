/*************************************
* Source code for temp executable
**************************************/
#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include <errno.h>
#include <stdlib.h>
#include <stdio.h>
#include <time.h>
#include <string.h>

#define  BUFSIZE  128

// ds18b20 sensor writes temperature to a file located here
char* addr = "/sys/bus/w1/devices/28-0316357079ff/w1_slave";

int main(int argc, char *argv[])
{
	int duration = atoi(argv[2]);
  char units;

	float temp;
	int i, j, k;
	int fd;
	int ret;

	char buf[BUFSIZE];
	char tempBuf[5];


for (k=0; k<duration; k = k+1){
  
  struct timespec tstart={0,0}, tend={0,0};
  clock_gettime(CLOCK_MONOTONIC, &tstart);

	// open file containing temperature reading
	fd = open(addr, O_RDONLY);

	if(-1 == fd){
		perror("open device file error");
		return 1;
	}

	// read the file in 128 byte chunks
	while(1){
		ret = read(fd, buf, BUFSIZE);
		if(0 == ret){
			break;
		}
		if(-1 == ret){
			if(errno == EINTR){
				continue;
			}
			perror("read()");
			close(fd);
			return 1;
		}
	}


	// temperature reading begins 2 characters after the letter 't' in the file,
	// and is 5 characters long
	for(i=0;i<sizeof(buf);i++){
		if(buf[i] == 't'){
			for(j=0;j<sizeof(tempBuf);j++){
				tempBuf[j] = buf[i+2+j];
			}
		}
	}

	// convert temperature to desired units
	temp = (float)atoi(tempBuf) / 1000;

  if(argc > 1)
  {  
  	units = *argv[1];

  	switch(units)
  	{
  		case 'c':
  		case 'C':
  			break;
  		case 'f':
  		case 'F':
  			temp = temp * 9/5 + 32;
  			break;
  		case 'k':
  		case 'K':
  			temp = temp + 273.15;
   		default:
  			temp = temp;
  	}
  }


	printf("%.3f\n",temp);

	close(fd);
  
  clock_gettime(CLOCK_MONOTONIC, &tend);


//  sleep(1);
}
	return 0;
}
