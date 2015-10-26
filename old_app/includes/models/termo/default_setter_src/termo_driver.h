#ifndef _UDAQ28LT_DRIVER_
#define _UDAQ28LT_DRIVER_

#include <usb.h>

// inicializuje zariadenie a vrati handle
usb_dev_handle* udaq28lt_init();

// nastavi vstupne napatia na zariadeni
void udaq28lt_write(usb_dev_handle *handle, double lamp, double led, double fan);

// nacita vystupne hodnoty zo zariadenia
// vrati -1 ak prisli chybne data. inak 0
int udaq28lt_read(usb_dev_handle *handle, double *temp, double *ftemp, double *light, double *flight, double *fan, double *fanrpm);

// posle na zariadenie nuly a ukonci komunikaciu
void udaq28lt_close(usb_dev_handle *handle);

usb_dev_handle* udaq28lt_init2();

void udaq28lt_write2(usb_dev_handle *handle, double pump1, double pump2, double valve1, double valve2, double valve3, double valve4, double valve5);

int udaq28lt_read2(usb_dev_handle *handle, double *h1, double *h2, double *h3, double *h_filt1, double *h_filt2, double *h_filt3, double *temp);

void udaq28lt_close2(usb_dev_handle *handle);

#endif
