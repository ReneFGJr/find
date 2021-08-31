<?php
class avaliations extends CI_model
    {
    function show($id='')
        {
            $im1 = '<img src="'.base_url('img/icon/star_full.png').'" style="height: 15px;">';
            $im2 = '<img src="'.base_url('img/icon/star_half.png').'" style="height: 15px;">';
            $im3 = '<img src="'.base_url('img/icon/star_off.png').'" style="height: 15px;">';
            $sx = $im1.$im1.$im1.$im2.$im3;
            return($sx);
        }  
    function heart($id='',$total=0)
        {
            $total = rand ( 2 , 2343 );
            $im1 = '<img src="'.base_url('img/icon/icone_heart.png').'" style="height: 15px;">';
            $sx = '<a href="#" class="btn btn-outline-secondary">'.$im1.' '.$total.'</a>';
            return($sx);
        }            
    }
?>
