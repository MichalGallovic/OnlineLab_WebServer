#include <stdio.h>
#include <usb.h>
#include <string.h>
//#include <machine.h>

#define VENDORID 1027
#define PRODUCTID 24577

#define VENDORID2 1027
#define PRODUCTID2 24577

struct usb_device *FindDevice() {
	// Najde udaq zariadenie a vrati jeho handle
	
	struct usb_device *dev = NULL;
	struct usb_bus *bus;
	usb_dev_handle *handle;
        char string[256];
        int ret;

	for (bus = usb_get_busses(); bus; bus = bus->next) {
		for (dev = bus->devices; dev; dev = dev->next) {
			handle = usb_open(dev);
			if (handle) {
				if ((dev->descriptor.idVendor == VENDORID)
					&& (dev->descriptor.idProduct == PRODUCTID)) {
                                            ret = usb_get_string_simple(handle, dev->descriptor.iProduct, string, sizeof(string));
                                            if (ret > 0){
                                                //printf("- Product : %s\n", string);
                                                if (strcmp("DIGICON uDAQ28/LT",string) == 0){//printf("true");
                                                    usb_close(handle);
                                                    return dev;
                                                }
                                            }
				}
			}
		}
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
			, (int)((led > 5.0 ? 5.0 : led < 0.0 ? 0.0 : led) * 51.0)
			, (int)((fan > 5.0 ? 5.0 : fan < 0.0 ? 0.0 : fan) * 51.0));
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
                //printf("error occoured\n");
		return -1;
	}

        //printf("%s",msg+2);
	sscanf(msg + 2, "%d %d %d %d %d %d", &(out[0]), &(out[1]), &(out[2]), &(out[3]), &(out[4]), &(out[5]));
	*temp = out[0] / 28.6705;
	*ftemp = out[1] / 28.6705;
	*light = out[2] / 40.95;
	*flight = out[3] / 40.95;
	*fan = out[4];
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

/************************************************************************************************************/
/** hydrosustava ********************************************************************************************/
/************************************************************************************************************/

struct usb_device *FindDevice2() {
	// Najde udaq zariadenie a vrati jeho handle

	struct usb_device *dev = NULL;
	struct usb_bus *bus;
	usb_dev_handle *handle;
        char string[256];
        int ret;

	for (bus = usb_get_busses(); bus; bus = bus->next) {
		for (dev = bus->devices; dev; dev = dev->next) {
			handle = usb_open(dev);
			if (handle) {
				if ((dev->descriptor.idVendor == VENDORID2)
					&& (dev->descriptor.idProduct == PRODUCTID2)) {
                                            ret = usb_get_string_simple(handle, dev->descriptor.iProduct, string, sizeof(string));
                                            if (ret > 0){
                                                //printf("- Product : %s\n", string);
                                                if (strcmp("DIGICON Hydraulic Plant",string) == 0){//printf("true");
                                                    usb_close(handle);
                                                    return dev;
                                                }
                                            }
				}
			}
		}
	}

	return dev;
}

usb_dev_handle* udaq28lt_init2() {
	// inicializuje zariadenie a vrati handle
	struct usb_device *dev;
	usb_dev_handle *handle = NULL;
	char msg[65];
	int ret;

	usb_init();
	usb_find_busses();
	usb_find_devices();

	dev = FindDevice2();
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

void udaq28lt_write2(usb_dev_handle *handle, double pump1, double pump2, double valve1, double valve2, double valve3, double valve4, double valve5) {
	// nastavi vstupne napatia na zariadeni
	char msg[65];

	sprintf(msg, "D%d,%d,%d,%d,%d,%d,%d\n"
			, (int)((pump1 > 1023.0 ? 1023.0 : pump1 < 0.0 ? 0.0 : pump1))
			, (int)((pump2 > 1023.0 ? 1023.0 : pump2 < 0.0 ? 0.0 : pump2))
			, (int)((valve1 == 1.0 ? 1.0 : 0.0))
			, (int)((valve2 == 1.0 ? 1.0 : 0.0))
			, (int)((valve3 == 1.0 ? 1.0 : 0.0))
			, (int)((valve4 == 1.0 ? 1.0 : 0.0))
			, (int)((valve5 == 1.0 ? 1.0 : 0.0)));
	usb_bulk_write(handle, 0x2, msg, strlen(msg), 100);
}

int udaq28lt_read2(usb_dev_handle *handle, double *h1, double *h2, double *h3, double *h_filt1, double *h_filt2, double *h_filt3, double *temp) {
	// nacita vystupne hodnoty zo zariadenia
	// vrati -1 ak prisli chybne data. inak 0
	int ret;
	char msg[65];
	int out[6];

	out[0]=0;out[1]=0;out[2]=0;out[3]=0;out[4]=0;out[5]=0;out[6]=0;

	ret = usb_bulk_read(handle, 0x81, msg, 64, 100);
	msg[ret] = 0;
	if (ret != (strchr(msg, '\n') - msg + 1)) {
                //printf("error occoured\n");
		return -1;
	}

        //printf("%s",msg+2);
	sscanf(msg + 2, "%d %d %d %d %d %d", &(out[0]), &(out[1]), &(out[2]), &(out[3]), &(out[4]), &(out[5]));
	*h1 = out[0];
	*h2 = out[1];
	*h3 = out[2];
	*h_filt1 = out[3];
	*h_filt2 = out[4];
	*h_filt3 = out[5];
	*temp = out[6];

	return 0;
}

void udaq28lt_close2(usb_dev_handle *handle) {
	// posle na zariadenie nuly a ukonci komunikaciu
	char msg[65];

	sprintf(msg, "D0,0,0,0,0,0,0\n");
	usb_bulk_write(handle, 0x2, msg, strlen(msg), 100);
	usb_bulk_read(handle, 0x81, msg, 64, 100);

	usb_release_interface(handle, 0);
	usb_close(handle);
}

/*
int main(int argc, char **argv) {
	usb_dev_handle *handle = NULL;
	int i;
	int l = 0;
	double temp, ftemp, light, flight, fan, fanrpm;

	handle = udaq28lt_init();

	for (i = 0; i < 100; i++) {
		udaq28lt_write(handle, 1, 1, 1);
		udaq28lt_read(handle, &temp, &ftemp, &light, &flight, &fan, &fanrpm);
		printf("%f %f %f %f %f %f\n", temp, ftemp, light, flight, fan, fanrpm);
	}

	udaq28lt_close(handle);

	return 0;
}
*/
