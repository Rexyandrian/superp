<?php
include('../kernel.php');
$pardakht = new pardakht_class(3528);
$tt = json_decode($pardakht->log_text);
//var_dump(json_decode($tmp->log_text));
//var_dump(moghim_class::loadReserveParam($tmp->log_text));
$moghim_info = moghim_class::reservefl($pardakht);
if($moghim_info->reserveflResult)
{    
    $tt = json_decode($pardakht->log_text);
    $info = $tt->ticket;
    //$parvaz =  $reserve_tmp->info['parvaz'];
    //if($parvaz->is_shenavar)
            //$shenavar[] = $parvaz;
    //var_dump($tt->parvaz);
    foreach($info as $ticket)
    {
        //var_dump($ticket);
        ticket_class::add_new($ticket, $moghim_info, $tt->rwaitlog,  json_encode($tt->parvaz), $ticket_id);
        /*
            $ticket->sanad_record_id = $sanad_record_id;
            if(!$ticket->add($res_tmp[0],$moghim_info,$pardakht->rwaitlog,$tt->parvaz,$ticket_id))
                    $ticket_error = TRUE;
            $ticket_ids[] = $ticket_id;
            if((int)$ticket->adult!=2)
                    $tedad++;
         * 
         */
    }
   
}

