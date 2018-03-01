<script src="<?php echo base_url('js/modernizr.js');?>"></script>
<style>
    .container {
        margin: 0 auto;
    }

    /* Example wrapper */
    .wrap {
        position: relative;
        margin: 3em 0;
    }

    /* Frame */
    .frame {
        height: 280px;//
        line-height: 350px;
        overflow: hidden;
    }
    .frame ul {
        list-style: none;
        margin: 0;
        padding: 0;
        height: 100%;//
        font-size: 50px;
    }
    .frame ul li {
        float: left;
        width: 227px;
        height: 100%;
        margin: 0 1px 0 0;
        padding: 5px;
        background: #fff;
        color: #000;
        text-align: center;
        cursor: pointer;
    }
    .frame ul li.active {
        color: #000000;
        background: #d0d0d0;
    }

    /* Scrollbar */
    .scrollbar {
        margin: 0 0 1em 0;
        height: 2px;
        background: #ccc;
        line-height: 0;
    }
    .scrollbar .handle {
        width: 100px;
        height: 100%;
        background: #292a33;
        cursor: pointer;
    }
    .scrollbar .handle .mousearea {
        position: absolute;
        top: -9px;
        left: 0;
        width: 100%;
        height: 20px;
    }

    /* Pages */
    .pages {
        list-style: none;
        margin: 20px 0;
        padding: 0;
        text-align: center;
    }
    .pages li {
        display: inline-block;
        width: 14px;//
        height: 14px;
        margin: 0 4px;
        text-indent: -999px;
        border-radius: 10px;
        cursor: pointer;
        overflow: hidden;
        background: #fff;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,.2);
    }
    .pages li:hover {
        background: #aaa;
    }
    .pages li.active {
        background: #666;
    }

    /* Controls */
    .controls {
        margin: 25px 0;
        text-align: center;
    }

    /* One Item Per Frame example*/
    .oneperframe {
        height: 300px;//
        line-height: 300px;
    }
    .oneperframe ul li {
        width: 1140px;
    }
    .oneperframe ul li.active {
        background: #333;
    }

    /* Crazy example */
    .crazy ul li:nth-child(2n) {
        width: 100px;
        margin: 0 4px 0 20px;
    }
    .crazy ul li:nth-child(3n) {
        width: 300px;
        margin: 0 10px 0 5px;
    }
    .crazy ul li:nth-child(4n) {
        width: 400px;
        margin: 0 30px 0 2px;
    }
</style>
<div class="wrap">
    <div class="wrap">
        <h2><?php echo $title_rs;?> <small>- <?php echo $title_cp;?></small></h2>

        <div class="scrollbar">
            <div class="handle">
                <div class="mousearea"></div>
            </div>
        </div>

        <div class="frame" id="basic">
            <ul class="clearfix">
                <?php echo $li; ?>
            </ul>
        </div>

        <ul class="pages"></ul>

        <div class="controls center">
            <button class="btn toStart">
                <?php echo msg('toStart');?>
            </button>

            <button class="btn prevPage">
                &lt;&lt;<i class="icon-chevron-left"></i> <?php echo msg('page');?>
            </button>

            <button class="btn prev">
                &lt; <?php echo msg('prev');?>
            </button>

            <button class="btn toCenter">
                <?php echo msg('toCenter');?>
            </button>

            <button class="btn next">
                <?php echo msg('next');?> &gt;</i>
            </button>

            <button class="btn nextPage">
                <?php echo msg('page');?> &gt;&gt;</i>
            </button>
            
            <button class="btn toEnd">
                <?php echo msg('toEnd');?>
            </button>
        </div>
    </div>
</div>


<!-- Scripts -->
<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script> -->
<script src="<?php echo base_url('js/plugins.js');?>"></script>
<script src="<?php echo base_url('js/sly.min.js');?>"></script>
<script src="<?php echo base_url('js/horizontal.js');?>"></script>