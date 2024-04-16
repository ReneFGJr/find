<?php
/*************************************************** INSTALADOR SQL */
function install($site)
    {
        $base = getenv('database.default.DBDriver');

        if ($base == 'MySQLi')
            {
                /** CHECAR SE EXISTE DataBase */

                $db = \Config\Database::connect();
                $dir = '../_documentation/sql/';
                $scan = scandir($dir);
                
                for ($r=0;$r < count($scan);$r++)
                    {
                        $file = $scan[$r];
                        if (strpos($file,'.sql'))
                            {
                                
                                $table = troca($file,'.sql','');
                                $table = troca($table,'_data','');

                                $dt = $db->query("SHOW TABLES LIKE '$table'")->getResult();

                                if (count($dt) == 0)
                                {
                                    $filename = $dir.$file;
                                    echo $filename.'<hr>';
                                    $sql = file_get_contents($filename);
                                    if (strpos($sql,'CREATE TABLE'))
                                        {
                                            $sql = substr($sql,strpos($sql,'CREATE TABLE'),strlen($sql));
                                            $sql = substr($sql,0,strpos($sql,') ENGINE')+1);                                        
                                        }
                                    $sql = troca($sql,'bigint(20) UNSIGNED','Serial');
                                    $db->query($sql);
                                }
                            }
                    }
                if (file_exists('.install'))
                    {
                        rename('.install','_install');
                    }
                echo metarefresh(PATH.MODULE);
                
            }
    }



