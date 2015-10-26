
//out_range je maximalna dosiahnutelna hodnota regulovanej veliciny 
select out_sw,
  case 1 then out_range=55.1;
  case 2 then out_range=45.1;
  case 3 then out_range=80.5;
  case 4 then out_range=77.2;
  case 5 then out_range=50;
  case 6 then out_range=6000;
  else printf("simulation problem"),
 end

printf("<br /><b>settings:</b><br />");
select in_sw,
  case 1 then printf("&nbsp;&nbsp;lamp = %.3f (init),<br />&nbsp;&nbsp;fan = %i,<br />&nbsp;&nbsp;led = %i,<br />&nbsp;&nbsp;sampling time = %i,<br />&nbsp;&nbsp;time = %i,<br />",(vstup*5/out_range),c_fan,c_led,ts,time);
  case 2 then printf("&nbsp;&nbsp;lamp = %i,<br />&nbsp;&nbsp;fan = %.3f (init),<br />&nbsp;&nbsp;led = %i,<br />&nbsp;&nbsp;sampling time = %i,<br />&nbsp;&nbsp;time = %i,<br />",c_fan,(vstup*5/out_range),c_led,ts,time);
  case 3 then printf("&nbsp;&nbsp;lamp = %i,<br />&nbsp;&nbsp;fan = %i,<br />&nbsp;&nbsp;led = %.3f (init),<br />&nbsp;&nbsp;sampling time = %i,<br />&nbsp;&nbsp;time = %i,<br />",c_fan,c_led,(vstup*5/out_range),ts,time);
  else printf("simulation problem"),
 end

select out_sw,
  case 1 then printf("&nbsp;&nbsp;final value of temp = %i (init)<br />",vstup);
  case 2 then printf("&nbsp;&nbsp;final value of ftemp = %i (init)<br />",vstup);
  case 3 then printf("&nbsp;&nbsp;final value of int = %i (init)<br />",vstup);
  case 4 then printf("&nbsp;&nbsp;final value of fint = %i (init)<br />",vstup);
  case 5 then printf("&nbsp;&nbsp;final value of I = %i (init)<br />",vstup);
  case 6 then printf("&nbsp;&nbsp;final value of RPM = %i (init)<br /><br />",vstup);
  else printf("simulation problem"),
 end

//printf("<br />&nbsp;&nbsp;Using own controller: %i <br />",own_ctrl);
loadScicosLibs();
abs_path = get_absolute_file_path("runner.sce");
tmpfile_path = abs_path+"tmp/tmpfile.txt";
exec(abs_path+"loader.sce");
warning('off');
exec(abs_path+"message.sci");
warning('on');
importXcosDiagram(abs_path+"termo.xcos");



Info=xcos_simulate(scs_m);

exit;