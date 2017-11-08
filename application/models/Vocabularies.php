<?php
class vocabularies extends CI_model
    {
    function modal_vc($id='')
        {
            $sx = '';
            if (strlen($id) > 0)
                {
                    $sx .= '
                    <!-- Button trigger modal -->
                    <form method="post" action="'.base_url('index.php/main/vocabulary/'.$id).'">
                    <a href="'.base_url('index.php/main/vocabulary').'" class="btn btn-secundary">Voltar</a>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
                      Inserir novo termo
                    </button>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Vocabulário controlado</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                                <span style="font-size:75%">Termo</span>
                                <input type="text" name="dd1" value="" class="form-control">                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <input type="submit" class="btn btn-primary" value="Gravar >>>">
                          </div>
                        </div>
                      </div>
                    </div>
                    </form>';
                }
        return($sx);            
        }
    function list_vc($id='')
        {
            $sx = '';
            /********************************************/
            if (strlen($id) == 0)
            {
                $sql = "select * from rdf_class 
                            WHERE c_type = 'C' 
                                    and c_vc = 1 
                            ORDER BY c_class ";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $sx = '<ul>';
                for ($r=0;$r < count($rlt);$r++)
                    {
                        $line = $rlt[$r];
                        $link = '<a href="'.base_url('index.php/main/vocabulary/'.$line['c_class']).'">';
                        $linka = '</a>';
                        $sx .= '<li>'.$link.msg($line['c_class']).$linka.'</li>';
                    }
                $sx .= '</ul>';
            } else {
                $ln = $this->frbr->data_class($id);
                $sx = '<ul>';
                for ($r=0;$r < count($ln);$r++)
                    {
                        $l = $ln[$r];
                        $link = '<a href="'.base_url('index.php/main/a/'.$l['id_cc']).'">';
                        $linka = '</a>';                        
                        $sx .= '<li>'.$link.$l['n_name'].$linka.'</li>';
                    }
                $sx .= '</ul>';
                return($sx);
            }
            return($sx);
        }      
    }
?>    
