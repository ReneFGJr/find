<?php
$st = array('none','none','none','none','none','none');
if ($pos < 1) { $pos = 1; }
if ($pos > count($st)) { $pos = (count($st)+1); }
//is-done, none, current
for ($r=1;$r <= $pos;$r++)
{
  $st[$r-1] = 'is-done';
}
$st[$pos-1] = 'current';
$link = base_url(PATH.'preparation/tombo/2/');
?>
  <ul class="StepProgress">
    <li class="StepProgress-item <?php echo $st[0];?>"><a href="<?php echo $link.'0';?>">Aquisição</a></li>
    <li class="StepProgress-item <?php echo $st[1];?>"><a href="<?php echo $link.'1';?>">Catalogação</a></li>
    <li class="StepProgress-item <?php echo $st[2];?>"><a href="<?php echo $link.'2';?>">Classificação</a></li>
    <li class="StepProgress-item <?php echo $st[3];?>"><a href="<?php echo $link.'3';?>">Indexação</a></li>
    <li class="StepProgress-item <?php echo $st[4];?>"><a href="<?php echo $link.'4';?>">Preparo físico</a></li>
    <li class="StepProgress-item <?php echo $st[5];?>"><a href="<?php echo $link.'5';?>">Fim do processo</a></li>
  </ul>

<style>
  .StepProgress {
    position: relative;
    padding-left: 25px;
    list-style: none;
    padding-right: 0px;
  }
  
  .StepProgress-item {
    position: relative;
    counter-increment: list;
  }
  .StepProgress-item:not(:last-child) {
    padding-bottom: 20px;
  }

  .StepProgress-item::before {
    display: inline-block;
    content: '';
    position: absolute;
    left: -26px;
    height: 100%;
    width: 10px;
  }

  .StepProgress-item::after {
    content: '';
    display: inline-block;
    position: absolute;
    top: 0;
    left: -37px;
    width: 24px;
    height: 24px;
    border: 2px solid #CCC;
    border-radius: 50%;
    background-color: #FFF;
  }

  .StepProgress-item.is-done {
  }
  .StepProgress-item.is-done::before {
    border-left: 2px solid green;
  }
  .StepProgress-item.is-done::after {
    content: "✔";
    font-size: 14px;
    color: #FFF;
    text-align: center;
    border: 2px solid green;
    background-color: green;
  }

  .StepProgress-item.current {
  }
  .StepProgress-item.current::before {
    
  }

  .StepProgress-item.current::after {
    content: counter(list);
    padding-top: 1px;
    width: 28px;
    height: 28px;
    top: -4px;
    left: -37px;
    font-size: 14px;
    text-align: center;
    color: green;
    border: 2px solid green;
    background-color: white;
  }

</style>