#include <time.h>
//#include "/home/mzolee/Plocha/xcos_projects/usb_model4/udaq28lt_driver.h"
#include <stdio.h>
#include <stdlib.h>
#include <scicos_block4.h>
#include <string.h>
#include <usb.h>
#define ENOFILE 2
//#include "libusb_dyn.c"

// toto by sa malo prehodit do .h suboru
#define usbVendorID	1027
#define usbProductID	24577

#define r_IN(n, i)	((GetRealInPortPtrs(blk, (n) + 1))[(i)])
#define r_OUT(n, i)	((GetRealOutPortPtrs(blk, (n) + 1))[(i)])

// vstupy
#define inLamp	(r_IN(0,0))	// lamp
#define inLed	(r_IN(1,0))	// led
#define	inFan	(r_IN(2,0))	// fan

// vystupy
#define outTemp		(r_OUT(0,0))	// temperature
#define outFiltTemp	(r_OUT(1,0))	// filtered temperature
#define outLight	(r_OUT(2,0))	// light intensity
#define outFiltLight	(r_OUT(3,0))	// filtered light intensity
#define outFan		(r_OUT(4,0))	// fan current
#define outFanRPM	(r_OUT(5,0))	// fan rpm

#define VENDORID 1027
#define PRODUCTID 24577

void wait(int ms) {
	clock_t endwait;

	endwait = clock() + ms;
	while (clock() < endwait);
}

void Log(char *text) {
	FILE *f;

	f = fopen("log.txt", "a");
	fprintf(f, "%s###\n", text);
	fclose(f);
}


struct usb_device *FindDevice() {
	// Najde udaq zariadenie a vrati jeho handle
	
	struct usb_device *dev = NULL;
	struct usb_bus *bus;
	usb_dev_handle *handle;
        char string[256];
        int ret;
	int not_found = 1;
	
	for (bus = usb_get_busses(); bus; bus = bus->next) {
		for (dev = bus->devices; dev; dev = dev->next) {
			handle = usb_open(dev);
			if (handle) {
				if ((dev->descriptor.idVendor == VENDORID)
					&& (dev->descriptor.idProduct == PRODUCTID)) {
                                            ret = usb_get_string_simple(handle, dev->descriptor.iProduct, string, sizeof(string));
                                            if (ret > 0){
                                                if (strcmp("DIGICON uDAQ28/LT",string) == 0){
						    printf("- Product : %s\n", string);
                                                    usb_close(handle);
						    not_found = 0;
                                                    return dev;
                                                }
                                            }
				}
			}
		}
	}
	if(not_found == 1){
		printf("\nerror: plant not connected");
	}
	return dev;
}

usb_dev_handle* udaq28lt_init() {
	// inicializuje zariadenie a vrati handle
	struct usb_device *dev;
	usb_dev_handle *handle = NULL;
	char msg[65];
	int ret;

	usb_init();
	usb_find_busses();
	usb_find_devices();

	dev = FindDevice();
	if (dev == NULL)
		return NULL;

	handle = usb_open(dev);

//#ifndef _WIN32
	usb_detach_kernel_driver_np(handle, 0);
//#endif

	usb_set_configuration(handle, 1);

	msg[0] = 0;
	usb_claim_interface(handle, 0);
	usb_control_msg(handle, 0x40, 0x3, 0xc, 0, msg, ret, 100);

	return handle;
}

void udaq28lt_write(usb_dev_handle *handle, double lamp, double led, double fan) {
	// nastavi vstupne napatia na zariadeni
	char msg[65];

	sprintf(msg, "S%d,%d,%d\n"
			, (int)((lamp > 5.0 ? 5.0 : lamp < 0.0 ? 0.0 : lamp) * 51.0)
			, (int)((fan > 5.0 ? 5.0 : fan < 0.0 ? 0.0 : fan) * 51.0)
			, (int)((led > 5.0 ? 5.0 : led < 0.0 ? 0.0 : led) * 51.0));
	usb_bulk_write(handle, 0x2, msg, strlen(msg), 100);
}

int udaq28lt_read(usb_dev_handle *handle, double *temp, double *ftemp, double *light, double *flight, double *fan, double *fanrpm) {
	// nacita vystupne hodnoty zo zariadenia
	// vrati -1 ak prisli chybne data. inak 0
	int ret;
	char msg[65];
	int out[6];

	out[0]=0;out[1]=0;out[2]=0;out[3]=0;out[4]=0;out[5]=0;

	ret = usb_bulk_read(handle, 0x81, msg, 64, 100);
	msg[ret] = 0;
	if (ret != (strchr(msg, '\n') - msg + 1)) {
		return -1;
	}

	sscanf(msg + 2, "%d %d %d %d %d %d", &(out[0]), &(out[1]), &(out[2]), &(out[3]), &(out[4]), &(out[5]));
	*temp = out[0] / 28.6705;
	*ftemp = out[1] / 28.6705;
	*light = out[2] / 40.95;
	*flight = out[3] / 40.95;
	*fan = out[4] / 81.92;
	*fanrpm = out[5];

	return 0;
}

void udaq28lt_close(usb_dev_handle *handle) {
	// posle na zariadenie nuly a ukonci komunikaciu
	char msg[65];

	sprintf(msg, "S0,0,0\n");
	usb_bulk_write(handle, 0x2, msg, strlen(msg), 100);
	usb_bulk_read(handle, 0x81, msg, 64, 100);

	usb_release_interface(handle, 0);
	usb_close(handle);
}

typedef struct {
	usb_dev_handle *handle;
	double last_outTemp;
	double last_outFiltTemp;
	double last_outLight;
	double last_outFiltLight;
	double last_outFan;
	double last_outFanRPM;
	int counter;
} han;

void termo(scicos_block *blk, int flag) {
	usb_dev_handle *handle=NULL;
	double blankOut;
	han *ptr;
	FILE *f;
	int errtest=0;
	int testcount=0;
	//int tmpvar=5;

	switch (flag) {

		case 4: //Initialization:
			//f = fopen("log.txt", "w");
			//fclose(f);
			handle = udaq28lt_init();
			udaq28lt_write(handle, 4, 4, 4);
			errtest = udaq28lt_read(handle, &blankOut, &blankOut, &blankOut, &blankOut, &blankOut, &blankOut);
			udaq28lt_write(handle, 0, 0, 0);
			errtest = udaq28lt_read(handle, &blankOut, &blankOut, &blankOut, &blankOut, &blankOut, &blankOut);
			
			*(blk->work) = (han*)malloc(sizeof(han));
			ptr = *(blk->work);
			ptr->handle = handle;
			
			ptr->last_outTemp	= 0;
			ptr->last_outFiltTemp	= 0;
			ptr->last_outLight	= 0;
			ptr->last_outFiltLight	= 0;
			ptr->last_outFan	= 0;
			ptr->last_outFanRPM	= 0;
			ptr->counter		= 0;
			//Log("\ninit");
			break;

		case 1: //OutputUpdate:
			//Log("\nwork1");
			
			ptr = *(blk->work);
			handle = ptr->handle;
			ptr->counter++;
			do{
				testcount++;
				udaq28lt_write(handle, inLamp, inLed, inFan);
				//Log("\nwork2");
				errtest = udaq28lt_read(handle, &outTemp, &outFiltTemp, &outLight, &outFiltLight, &outFan, &outFanRPM);
			}while(errtest == -1 && testcount <= 20);
			if (testcount == 20 || ( ptr->counter > 5 && (abs(outFiltLight - ptr->last_outFiltLight) > 2 ||abs(outTemp - ptr->last_outTemp) > 2 ||abs(outFiltTemp - ptr->last_outFiltTemp) > 2 )))
			{
				outTemp		= ptr->last_outTemp;
				outFiltTemp	= ptr->last_outFiltTemp;
				outLight	= ptr->last_outLight;
				outFiltLight	= ptr->last_outFiltLight;
				outFan		= ptr->last_outFan;
				outFanRPM	= ptr->last_outFanRPM;
			}
			ptr->last_outTemp	= outTemp;
			ptr->last_outFiltTemp	= outFiltTemp;
			ptr->last_outLight	= outLight;
			ptr->last_outFiltLight	= outFiltLight;
			ptr->last_outFan	= outFan;
			ptr->last_outFanRPM	= outFanRPM;
			//printf("%f %f %f %f %f %f\n", temp, ftemp, light, flight, fan, fanrpm);
			//printf('\nvstup');
			
// 		outTemp = tmpvar;
// 		outFiltTemp = tmpvar;
// 		outLight = tmpvar;
// 		outFiltLight = tmpvar;
// 		outFan = tmpvar;
// 		outFanRPM = tmpvar;
			//Log("\nwork3");
			break;

		case 5: //Ending:
			ptr = *(blk->work);
			handle = ptr->handle;
			udaq28lt_close(handle);
			free(*(blk->work));
			//Log("\nend");
			break;
	}
}
