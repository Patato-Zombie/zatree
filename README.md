Zatree���
==================================
��zabbix�߽�ǧ���򻧵�ʱ������Ҳ����Ҫ����ʲô��

zatree�Ǽ�����zabbix��һ���������Ҫ�������ṩhost group������չʾ����item��ָ���ؼ��ֲ�ѯ����������

���Թ�2.0.6��2.0.7�汾��������2.0.X�Ķ�Ӧ��֧�֣������֧����Ҳû�취���Լ����ָĸİɡ�

Zatree��װ
==================================
1�������ļ�

git clone https://github.com/spide4k/zatree.git zatree

2����������ļ�

����zabbix webĿ¼λ����/var/www/zabbix,����zabbixĿ¼

ZABBIX_PATH=/var/www/zabbix

��������ļ���Ŀ¼

cp -rf zatree $ZABBIX_PATH/

cd $ZABBIX_PATH/zatree/addfile

cp class.cchart_zabbix.php class.cgraphdraw_zabbix.php class.cimagetexttable_zabbix.php $ZABBIX_PATH/include/classes/

cp zabbix.php zabbix_chart.php $ZABBIX_PATH/

cp CItemValue.php $ZABBIX_PATH/api/classes/

3��֧��web interface,�޸������ļ�

vi $ZABBIX_PATH/zatree/zabbix_config.php

'user'=>'xxx', //web��½���û���

'passowrd'=>'xxx', //web��½������

4����������Zatree���,�޸�menu.inc.php��main.js

vi $ZABBIX_PATH/include/menu.inc.php

���285�е�294������

    285         'zatree'=>array(
    286                 'label' => _('Zatree'),
    287                 'user_type'                             => USER_TYPE_ZABBIX_USER,
    288                 'default_page_id'       => 0,
    289                 'force_disable_all_nodes' => true,
    290                 'pages' =>array(
    291                         array('url' => 'zabbix.php','label' => _('Zatree'),)
    292                         )
    293         
    294         ),      
    295         
    296         'login' => array(                               
    297                 'label'                                 => _('Login'),
    298                 'user_type'                             => 0,
    299                 'default_page_id'               => 0,

vi $ZABBIX_PATH/js/main.js

�滻106��

menus:                  {'empty': 0, 'view': 0, 'cm': 0, 'reports': 0, 'config': 0, 'admin': 0, 'zatree':0},

6�����ӷ�װ��api��


vi $ZABBIX_PATH/include/classes/api/API.php

��74�������75��'itemvalue'=>'CItemValue',

     74                 'usermedia' => 'CUserMedia',
     75                 'itemvalue'=>'CItemValue',
     76                 'webcheck' => 'CWebCheck'
     77         ); 

7����½zabbix���ڵ�������Կ���һ��Zatree�Ĳ˵���ʹ�÷�����ɵ�ϵ�

��������
==================================
1������Ŵ�

���Դ�php����ʾ���󣬿���ʲôԭ��

vi /etc/php.ini

display_errors = On

����web server,Ȼ����web��־

2��Fatal error: Call to undefined function json_encode() in /var/www/html/zabbix/zatree/ZabbixApiAbstract.class.php on line 220

��Ҫphp encode֧��

yum install php-pecl-json

�����������������У��Ҳ���php-pecl-json�����������������

yum install php-pear

pecl install json

echo "extension=json.so" > /etc/php.d/json.ini

3����������ʾһ��2��ͼ��˵����ֱ��ʲ��������ϰ���㻻������

4�����������Сͼ����ʾʱ��Σ��༭�ļ�include/classes/class.cchart_zabbix.php��ע��2363��

     2363                 //      $this->drawDate();

����֧��
==================================
http://weibo.com/spider4k

http://weibo.com/chinahanna

http://weibo.com/678236656

ף������

