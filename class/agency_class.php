<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of agency_class
 *
 * @author hamed
 */
class agency_class {
    public function __construct($moghim_code = -1)
    {
            $mysql = new mysql_class;
            $mysql->ex_sql("select * from `agency` where `moghim_code` = '$moghim_code'",$q);
            foreach($q[0] as $fi=>$val)
            {    
                $this->$fi=$val;
            }
    }
}
