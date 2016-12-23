/*******************************
*    Ultra Sonic Raning module Pin VCC should
*  be connected to 5V power.
******************************/
#include <wiringPi.h>
#include <stdio.h>
#include <sys/time.h>

#define Trig    0
#define Echo    1

void ultraInit(void)
{
	pinMode(Echo, INPUT);
	pinMode(Trig, OUTPUT);
}

float disMeasure(void)
{
	struct timeval tv1;
	struct timeval tv2;
	long time1, time2;
    float dis;

	digitalWrite(Trig, LOW);
	delayMicroseconds(2);

	digitalWrite(Trig, HIGH);
	delayMicroseconds(10);     
	digitalWrite(Trig, LOW);
								
	while(!(digitalRead(Echo) == 1));
	gettimeofday(&tv1, NULL);  

	while(!(digitalRead(Echo) == 0));
	gettimeofday(&tv2, NULL);

	time1 = tv1.tv_sec * 1000000 + tv1.tv_usec;
	time2  = tv2.tv_sec * 1000000 + tv2.tv_usec;

	dis = (float)(time2 - time1) / 1000000 * 34000 / 2;

	return dis;
}

int main(int argc, char *argv[])
{
  int units = 0;
  float dis;

  if(argc > 1)
  {
    units = *argv[1] - '0';
  }

	if(wiringPiSetup() == -1){ //when initialize wiringPi fails,print messageto screen
		printf("setup wiringPi failed !");
		return 1; 
	}

	ultraInit();
	
	dis = disMeasure();
  
  if(units == 1)
  {
    dis = dis * 0.393701;
  }

	printf("%0.2f\n",dis);

   return 0;
}
