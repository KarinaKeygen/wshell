cat /proc/cpuinfo - инфа о cpu

uname -a - инфа о системе
update-pciids - обновить данные по pci-устройствам


ls* - команды для просмотра инфы о системе
	lsb_release -a - инфа о сборке
	lspci - инфа о внутренних устройствах
	 lspci -v -s 00:02.0 - по устройству 00:02.0
	lshw -c video - инфа о видеокарте
	lsusb - список USB устройств
	lsmod - статус модулей ядра

dmesg - kernel log

tar -xvf archive.tar
