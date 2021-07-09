<?php
    $file = 'img/logo/footer_'.LIBRARY.'.jpg';
    $footer = '';
    if (file_exists($file))
        {
            $footer .= '<div class="'.bscol(12).'">';
            $footer .= '<img src="'.base_url($file).'" class="img-fluid">';
            $footer .= '</div>'.cr();
        }

?>
<div class="container-fluid footer">
	<div class="row">
        <?php echo $footer; ?>
		<div class="col-md-12">
		    <div class="container small">			
			<ul style="list-style: none;">
                <li><a href="<?php echo base_url(PATH);?>" class="link"><?php echo msg('Catalog');?></a></li>
                <li><a href="#" class="link"><?php echo msg('About');?></a></li>
			    <li><a href="#" class="link"><?php echo msg('Contact');?></a></li>

                <li><a href="<?php echo base_url(PATH.'indexes');?>" class="link"><?php echo msg('indexes');?></a></li>
                <ul style="list-style: none;">
                    <li><a href="<?php echo base_url(PATH.'indexes/authors');?>" class="link"><?php echo msg('index_authors');?></a></li>
                    <li><a href="<?php echo base_url(PATH.'indexes/subject');?>" class="link"><?php echo msg('index_subject');?></a></li>
                    <li><a href="<?php echo base_url(PATH.'find');?>" class="link"><?php echo msg('about_find');?></a></li>
                </ul>
			</ul>
			</div>
		</div>
	</div>
</div>
<style>
    .footer
    {
        border-top: 1px solid #333333; 
        margin-top: 150px; 
        min-height: 200px;"    
    }
    .link
        {
            color: #808080;
            margin: 0px;
            padding: 0px;
        }
    .link:hover
        {
            color: #000000;
        }
</style>