<?php
    $sx = '
        <div class="container">
            <div class="row">
                <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
                    <tt style="font-size: 100%;">'.msg('Expression').'</tt>
                </div>
                <div class="col-md-10">
                        <a href="'.base_url('index.php/main/expression_create/'.$id).'" class="btn btn-secondary">Nova expressão</a>
                </div>
            </div>
        </div>
        <br>    
    ';
    echo $sx;
?>