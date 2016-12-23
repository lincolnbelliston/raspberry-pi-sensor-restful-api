#include "bmp180.h"
#include <unistd.h>
#include <stdio.h>

int main(int argc, char *argv[]){
  
	char *i2c_device = "/dev/i2c-1";
	int address = 0x77;
  char tunits;
  int punits;
  int aunits;

  // units for temperature, pressure, altitude
  tunits = *argv[1];
  punits = *argv[2] - '0';
  aunits = *argv[3] - '0';  

	void *bmp = bmp180_init(address, i2c_device);

	if(bmp != NULL){

      // degrees C, pascals, meters
			float t = bmp180_temperature(bmp);
			float p = (float)bmp180_pressure(bmp);
			float alt = bmp180_altitude(bmp);
	
      switch(tunits)
      {
        case 'c':
          break;
        case 'f':
          t = t * 9/5 +32; // convert to Fahrenheit
          break;
        case 'k':
          t = t + 273.15; // convert to Kelvin
      }

      switch(punits)
      {
        case 0:
          break;
        case 1:
          p = p * 0.00000986923; // convert to atm
          break;
        case 2:
          p = p * 0.000145038; // convert to psi
      }

      if(aunits == 1)
      {
        alt = alt * 3.48084; // convert to feet
      }

      printf("%.1f_%.5f_%.0f\n", t, p, alt);
    
      bmp180_close(bmp);
	}
  else
  {
    printf("bmp is null\n");
  }
  
	return 0;
}
