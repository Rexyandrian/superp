<?php
    include_once("../kernel.php");
    $reserve_tmp = new reserve_tmp_class(114);
    $info = $reserve_tmp->info['info'];
    $fnames='';
    $lnames='';
    $sexkind='';
    $passkind='';
    $mellicode='';
    $passport ='';
    $mobile ='';
    $i=0;
    foreach($info as $r)
    {
        
        $fnames.= (($fnames=='')?'':'|').$r->fname;
        $lnames.= (($lnames=='')?'':'|').$r->lname;
        $sexkind.= (($sexkind=='')?'':'|'). ($r->gender==0?'MR':'MRS');
        switch ($r->adult) {
            case 0:
                $tmp_passkind = 'ADL';
                break;
            case 1:
                $tmp_passkind = 'CHD';
                break;
            case 2:
                $tmp_passkind = 'INF';
                break;
            default:
                break;
        }
        $passkind.= (($passkind=='')?'':'|').$tmp_passkind;
        $mellicode.= (($mellicode=='')?'':'|').$r->code_melli;
        $passport.= (($passport=='')?'':'|').$r->code_melli;
        if($i==0)
        {    
            $mobile =$r->tel;
        }    
        $i++;
    }    
    $out = array('fname'=>$fnames,'lname'=>$lnames,'sexkind'=>$sexkind,'passkind'=>$passkind,'mellicode'=>$mellicode,'passport'=>$passport,'mobile'=>$mobile);
