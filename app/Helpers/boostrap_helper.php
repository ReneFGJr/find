<?php
/**
* CodeIgniter Form Helpers
*
* @package     CodeIgniter
* @subpackage  BootStrap
* @category    Helpers
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.sisdoc.com.br/CodIgniter
* @version     v0.21.07.27
*/


function bssmall($t)
    {
        $sx = '<small>'.$t.'</small>';
        return $sx;
    }

function bscontainer($fluid=0)
    {
        $class = "container";
        if ($fluid == 1) { $class = "container-fluid"; }
        $sx = '<div class="'.$class.'">';
        return($sx);
    }

function bsrow()
    {
        $sx = '<div class="row">';
        return($sx);
    }       

function bscarousel($d)
    {
        $sx = '';
        $sx .= '<div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">'.cr();
        $sx .= '<div class="carousel-inner">'.cr();
        for ($r=0;$r < count($d);$r++)
            {
                $act = '';
                if ($r==0) { $act = 'active'; }
                $line = $d[$r];
                $img = $line['image'];
                $sx .= '
                <div class="carousel-item '.$act.'">
                    <img class="d-block w-100" src="'.$img.'" alt="Slide '.($r+1).'" style="height: 300px;">
                </div>'.cr();
            }
        $sx .= '</div>'.cr();
        $sx .= '
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
      </div>';

      $sx = '
      <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">'.cr();
        for ($r=0;$r < count($d);$r++)
            {
                $sx .= '<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="'.$r.'" class="active" aria-current="true" aria-label="Slide '.($r+1).'"></button>'.cr();
            }
        $sx .= '</div>

        <div class="carousel-inner">'.cr();

        for ($r=0;$r < count($d);$r++)
            {
                $act = '';
                if ($r==0) { $act = 'active'; }
                $line = $d[$r];
                $img = $line['image'];
                $sx .= '
                <div class="carousel-item '.$act.'">
                    <img class="d-block w-100" src="'.$img.'" alt="Slide '.($r+1).'" style="height: 300px;">
                </div>'.cr();
            }        
        $sx .= '
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>      
      ';
      return $sx;
    }

function bs($t,$dt=array())
    {
        $fluid = 0;
        if (isset($dt['fluid'])) { $fluid = $dt['fluid']; }
        $html = brow($t);
        $html = bcontainer($html, $fluid);
        return($html);
    }

function bcontainer($t,$fluid=0)
    {
        $class = "container";
        if ($fluid == 1) { $class = "container-fluid"; }
        $sx = '<div class="'.$class.'">';
        $sx .= $t;
        $sx .= '</div>';
        return $sx;return($sx);
    }

function brow($t,$dt=array())
    {
        $opt = '';
        $sx = '<div class="row '.$opt.'">';
        $sx .= $t;
        $sx .= '</div>';
        return $sx;
    }

function bsc($t,$grid=12,$class='')
    {
        $sx = '';
        $sx .= bscol($grid,$class);
        $sx .= $t;
        $sx .= '</div>';
        return $sx;
    }

function bscard($title='Title',$desc='Desc')
    {
        $sx = '<div class="card mt-1">
        <!--
        <img class="card-img-top" src="..." alt="Card image cap">
        -->
        <div class="card-body">
            <h5 class="card-title">'.$title.'</h5>
            <p class="card-text">'.$desc.'</p>
        </div>
        </div>';  
        return $sx;
    }

function bsclose($n=0)
    {
        $sx = '';
        for ($r=0;$r < $n; $r++)
            {
                $sx .= bsdivclose().cr();
            }
        return($sx);
    }
function bsmessage($txt,$t=0)
    {
        $sx = '
            <div class="alert alert-primary" role="alert">
            '.$txt.'
            </div>';      
        $sx .= cr();
        return($sx);
    }


function bsdivclose()
    {
        return('</div>');
    }
function h($t='',$s=1,$class='')
    {
        $sx = '<h'.$s.' class="'.$class.'">'.$t.'</h'.$s.'>';
        return($sx);
    }  

function bscol($c,$class='')
    {
        switch($c)
            {

                case '1':
                    $sx = '';
                    $sx .= ' col-4';        /* < 756px  */
                    $sx .= ' col-sm-3';     /* > 576px  */
                    $sx .= ' col-md-3';     /* > 768px  */
                    $sx .= ' col-lg-1';     /* > 992px  */
                    $sx .= ' col-xl-1';     /* > 1200px */
                break;                 

                case '2':
                    $sx = '';
                    $sx .= ' col-12';        /* < 756px  */
                    $sx .= ' col-sm-12';     /* > 576px  */
                    $sx .= ' col-md-6';     /* > 768px  */
                    $sx .= ' col-lg-2';     /* > 992px  */
                    $sx .= ' col-xl-2';     /* > 1200px */
                break;   

                case '3':
                    $sx = 'col-md-3';
                    $sx .= ' col-3';
                    $sx .= ' col-sm-6';
                    $sx .= ' col-lg-3';
                    $sx .= ' col-xl-3';
                break; 

                case '4':
                    $sx = '';
                    $sx .= ' col-6';        /* < 756px  */
                    $sx .= ' col-sm-6';     /* > 576px  */
                    $sx .= ' col-md-4';     /* > 768px  */
                    $sx .= ' col-lg-4';     /* > 992px  */
                    $sx .= ' col-xl-4';     /* > 1200px */
                break;                  

                case '5':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-5';
                    $sx .= ' col-lg-5';
                    $sx .= ' col-xl-5';
                break;

                case '6':
                    $sx = 'col-md-6';
                    $sx .= ' col-6';
                    $sx .= ' col-sm-6';
                    $sx .= ' col-lg-6';
                    $sx .= ' col-xl-6';
                break; 

                case '7':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-7';
                    $sx .= ' col-lg-7';
                    $sx .= ' col-xl-7';
                break;

                case '8':
                    $sx = '';
                    $sx .= ' col-12';        /* < 756px  */
                    $sx .= ' col-sm-12';     /* > 576px  */
                    $sx .= ' col-md-8';     /* > 768px  */
                    $sx .= ' col-lg-8';     /* > 992px  */
                    $sx .= ' col-xl-8';     /* > 1200px */
                break; 

                case '9':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-9';
                    $sx .= ' col-lg-9';
                    $sx .= ' col-xl-9';
                break;                                                                          

                case '10':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-10';
                    $sx .= ' col-lg-10';
                    $sx .= ' col-xl-10';
                break;

                case '11':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-11';
                    $sx .= ' col-lg-11';
                    $sx .= ' col-xl-11';
                break;  

                case '12':
                    $sx = '';
                    $sx .= ' col-12';        /* < 756px  */
                    $sx .= ' col-sm-12';     /* > 576px  */
                    $sx .= ' col-md-12';     /* > 768px  */
                    $sx .= ' col-lg-12';     /* > 992px  */
                    $sx .= ' col-xl-12';     /* > 1200px */
                break;                 


                default:
                    $c = sonumero($c);
                    $sx = 'col-md-'.$c;
                    $sx .= ' col-'.$c;
                    $sx .= ' col-sm-'.$c;
                    $sx .= ' col-lg-'.$c;
                    $sx .= ' col-xl-'.$c;
                break;
            }
        return('<div class="'.$sx.' '.$class.'">');
    }
function bs_pages($ini,$stop,$link='')
    {
        $sx = '';
        $sx .= '<nav aria-label="Page navigation example">'.cr();
        $sx .= '<ul class="pagination">'.cr();
        for ($r=$ini;$r <= $stop;$r++)
            {
                $xlink = base_url($link.'/'.chr($r));
                $sx .= '<li class="page-item"><a class="page-link" href="'.$xlink.'">'.chr($r).'</a></li>'.cr();
            }
        $sx .= '</ul>';
        $sx .= '</nav>';
        return($sx);
    }
function bs_alert($type = '', $msg = '') {
    $ok = 0;
    switch($type) {
        case 'success' :
            $ok = 1;
            break;
        case 'secondary' :
            $ok = 1;
            break;
        case 'danger' :
            $ok = 1;
            break;
        case 'warning' :
            $ok = 1;
            break;
        case 'info' :
            $ok = 1;
            break;
        case 'light' :
            $ok = 1;
            break;
        case 'dark' :
            $ok = 1;
            break;
        default :
            $sx = 'TYPE: primary, secondary, success, danger, warning, info, light, dark';
    }
    if ($ok == 1) {
        $sx = '<br><div class="alert alert-' . $type . '" role="alert">
                ' . $msg . '
               </div>' . cr();
    }
    return($sx);
}    