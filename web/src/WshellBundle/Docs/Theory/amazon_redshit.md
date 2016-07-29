### Введение

Amazon Redshift - это облачное хранилище на over 1Pb данных.
Причем эти данные не просто храняться, но и эффективно обрабатываются.
Запросы автоматически распараллеливаются, данные бэкапятся.


### SQL

Redshift имеет сильно ограниченные возможности по сравнению с PosgreSQL,
на котором он основан( ver. 8.0.2)

На lider_node и computer_mode разные запросы. В частности, следующие функции доступны
ТОЛЬКО для lider_node:
http://docs.aws.amazon.com/redshift/latest/dg/c_SQL_functions_leader_node_only.html


Типы данных:
http://docs.aws.amazon.com/redshift/latest/dg/c_Supported_data_types.html





The maximum size for a single SQL statement is 16 MB.
