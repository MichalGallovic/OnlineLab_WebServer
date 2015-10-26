#include <stdio.h>
#include <unistd.h>
#include <time.h>
#include "termo_driver.h"

void wait(int ms) {
	clock_t endwait;

	endwait = clock() + ms;
	while (clock() < endwait);
}


int main(int argc, char **argv) {
	usb_dev_handle *handle = NULL;
	int i;
        int errtest=0;
        int testcount=0;
        int time_to_empty=60;   //v sekundach
/**/	double temp, ftemp, light, flight, fan, fanrpm;

	handle = udaq28lt_init();
	for (i = 0; i < 2; i++) {
                do{
                    testcount++;
                    udaq28lt_write(handle, 0, 0, 0);
                    errtest = udaq28lt_read(handle, &temp, &ftemp, &light, &flight, &fan, &fanrpm);
                }while(errtest == -1 && testcount < 20);
                sleep(1);
                testcount=0;
                //printf("%f %f %f %f %f %f\n", temp, ftemp, light, flight, fan, fanrpm);

	}
        udaq28lt_write(handle, 0, 0, 0);
        errtest = udaq28lt_read(handle, &temp, &ftemp, &light, &flight, &fan, &fanrpm);
        //printf("\nending\n%f %f %f %f %f %f\n", temp, ftemp, light, flight, fan, fanrpm);

        printf("\nsystem was set to default\n");
	udaq28lt_close(handle);
/*
	double  h1, h2, h3, h_filt1, h_filt2, h_filt3, temp;

	handle = udaq28lt_init2();
      was
	for (i = 0; i < time_to_empty; i++) {
                do{
                    testcount++;
                    udaq28lt_write2(handle, 0, 0, 1, 1, 1, 1, 1);
                    errtest = udaq28lt_read2(handle, &h1, &h2, &h3, &h_filt1, &h_filt2, &h_filt3, &temp);
                }while(errtest == -1 && testcount < 20);
                sleep(1);
                testcount=0;
                //printf("%f %f %f %f %f %f %f\n", h1, h2, h3, h_filt1, h_filt2, h_filt3, temp);
	}

        udaq28lt_write2(handle, 0, 0, 0, 0, 0, 0, 0);
        errtest = udaq28lt_read2(handle, &h1, &h2, &h3, &h_filt1, &h_filt2, &h_filt3, &temp);
        //printf("\nending\n%f %f %f %f %f %f %f\n", h1, h2, h3, h_filt1, h_filt2, h_filt3, temp);

        printf("\nsystem has bin set to default\n");
	udaq28lt_close2(handle);
/**/
	return 0;
}
