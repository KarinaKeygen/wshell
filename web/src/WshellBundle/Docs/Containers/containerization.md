Node = ClasterNetwork + Containers

Containers:
Docker => LXC => cgroups (часть ядра linux > 2.6.24)


Docker - ПО для автоматизации развёртывания и управления приложениями
в среде виртуализации на уровне операционной системы

https://linuxcontainers.org/ru/
LXC -  система виртуализации на уровне операционной системы для запуска
нескольких изолированных экземпляров операционной системы Linux на одном узле
Достаточно сложна и docker её существенно упрощает

cgroups - механизм ядра Linux, который ограничивает и изолирует вычислительные ресурсы
(процессорные, сетевые, ресурсы памяти, ресурсы ввода-вывода) для групп процессов


https://upload.wikimedia.org/wikipedia/commons/e/e7/Linux_kernel_unified_hierarchy_cgroups_and_systemd.svg?uselang=ru
cgroups позволяет образовывать иерархические группы процессов с заданными ресурсными свойствами
и обеспечивает программное управление ими:
* ограничение ресурсов
* приоритезация(~процессорное время)
* учёт затрат
* ИЗОЛЯЦИЮ
* приостановка групп, создание чекпоинтов

Контрольная группа (cgroup) — набор процессов, объединённых по некоторым признакам,
группировка может быть иерархической с наследованием ограничений и параметров родительской группы.
Ядро Linux предоставляет доступ ко множеству так называемых контроллеров (подсистем) через интерфейс cgroup,
например, контроллер «memory» ограничивает использование оперативной памяти, контроллер «cpuacct» учитывает
использование процессорного времени.

Управление cgroups осуществляется через работу с вирт файловой системой cgroups
При каждом монтировании опциями указывается список подсистем для управления
ресурсами
https://www.kernel.org/doc/Documentation/cgroups/cgroups.txt

Пользовательский код может создавать и уничтожать по имени в экземляре
виртуальной файловой системы cgroups, определять какой группе назначена задача и получать
список PID связанных с cgroups.
Для управления конкретным видом ресурсов cgroups предлагает набор хуков. например
Documentation/cgroups/cpusets.txt используется для связи CPU и Memory с задачами.

Ресурс трекинг: cpusets, CKRM/ResGroups, UserBeanCounters, and virtual server
namespaces
Пример разделения ресурсов:

       CPU :          "Top cpuset"
                       /       \
               CPUSet1         CPUSet2
                  |               |
               (Professors)    (Students)

               In addition (system tasks) are attached to topcpuset (so
               that they can run anywhere) with a limit of 20%

       Memory : Professors (50%), Students (30%), system (20%)

       Disk : Professors (50%), Students (30%), system (20%)

       Network : WWW browsing (20%), Network File System (60%), others (20%)
                               / \
               Professors (15%)  students (5%)

Если использовать несколько иерархий, становиться возможным несколько
альтернативных распределений ресурсов. Например, временное предоставление
ресурсов студенту для лабораторной работы.


Реализация cgroups требует нескольких простых хуков в другие части ядра.
Директория для каждой иерархии содержит:
* tasks (PIDs)
* cgroup.procs - список потоков
* notify_on_release flag + release_agent - поведение при освобождении ресурсов
Различные подсистемы могут добавлять свои файлы

(1.6 How do I use cgroups ?)

Для примера, следующая последовательность команд создаст cgroups "MyTask",
содержащую 2и3 CPU и memory node 1 и запустит шелл в ней:

mount -t tmpfs cgroup_root /sys/fs/cgroup
mkdir /sys/fs/cgroup/cpuset
mount -t cgroup cpuset -ocpuset /sys/fs/cgroup/cpuset
cd /sys/fs/cgroup/cpuset
mkdir MyTask
cd MyTask
/bin/echo 2-3 > cpuset.cpus
/bin/echo 1 > cpuset.mems
/bin/echo $$ > tasks
sh
# The subshell 'sh' is now running in cgroup Charlie
# The next line should display '/MyTask'
cat /proc/self/cgroup



http://habrahabr.ru/search/?target_type=posts&q=cgroup&order_by=rating
Примеры (монтирование группы->аттач процессов)

монтирование со всеми подсистемами
mount -t cgroup xxx /sys/fs/cgroup

или определенными
mount -t cgroup -o cpuset,memory hier1 /sys/fs/cgroup/rg1

с release_agent
mount -t cgroup -o cpuset,release_agent="/sbin/cpuset_release_agent" xxx /sys/fs/cgroup/rg1

LXC
"Docker довольно узкоспециализирован. Для изоляции приложений и даже деплоя он, конечно, хорош. Но когда нужен контейнер с полноценной операционной системой, лучше все-таки смотреть на lxc или openvz."

chroot (недостатки)
Только суперпользователь (root) может выполнять системный вызов chroot(2). Это необходимо для того, чтобы предотвратить атаку со стороны пользователя при помощи помещения setuid-ной программы внутри специально изготовленной chroot jail (например, с ложным файлом /etc/passwd) что будет приводить к тому, что они получат повышение привилегий.
Сам по себе механизм chroot не полностью безопасен. Если программа, запущенная в chroot имеет привилегии root, она может выполнить second chroot для того, чтобы выбраться наружу. Это работает потому, что некоторые ядра Unix не позволяют правильного вложения контекстов chroot.
Большинство систем Unix не полностью ориентированы на файловую систему и оставляют потенциально разрушительную функциональность, такую как сетевую и контроль процессов доступной через интерфейс системных вызовов к программе в chroot.
Механизм chroot сам по себе не умеет осуществлять лимитирования на ресурсы, такие как пропускная способность ввода-вывода, дисковое пространство или время ЦП.
http://www.bpfh.net/simes/computing/chroot-break.html

http://habrahabr.ru/post/272145/
(перевод http://merrigrove.blogspot.co.uk/2015/10/visualizing-docker-containers-and-images.html)

http://habrahabr.ru/company/westcomp/blog/269423/
http://habrahabr.ru/post/267441/
