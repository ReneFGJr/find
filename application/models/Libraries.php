<?php
class libraries extends CI_model {
    function __construct() {
        define('LIBRARY', '1002');
        define('PATH', 'index.php/books/');
        define('LOGO', 'img/logo-brapci_livros_mini.png');
        define('LIBRARY_NAME', 'Brapci Livros');
        define('LIBRARY_LEMA', 'Memória da Ciência da Informação');
        define('BARS', LIBRARY . '0000000');
    }

}
?>
