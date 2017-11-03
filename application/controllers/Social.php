<?php
// This file is part of the Brapci Software.
//
// Copyright 2015, UFPR. All rights reserved. You can redistribute it and/or modify
// Brapci under the terms of the Brapci License as published by UFPR, which
// restricts commercial use of the Software.
//
// Brapci is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the ProEthos License for more details.
//
// You should have received a copy of the Brapci License along with the Brapci
// Software. If not, see
// https://github.com/ReneFGJ/Brapci/tree/master//LICENSE.txt
/* @author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
 * @date: 2015-12-01
 */
class social extends CI_Controller {

    /* Google */
    var $auth_google = 1;
    var $google_redirect = 'http://www.brapci.inf.br/oauth_google.php';
    var $google_key = '205743538602-t6i1hj7p090g5jd4u70614vldnhe7143.apps.googleusercontent.com';
    var $google_key_client = 'AMhQ7Vfc7Lpzi_ZVZKq4wbWV';
    /* Windows */
    var $auth_microsoft = 1;
    var $microsoft_id = '0000000040124367';
    var $microsoft_key = 'JOlz8eVtECgfKt0MKTg0I-aXZrUboW21';

    /* Facebook */
    var $auth_facebook = 1;
    var $face_id = '547858661992170';
    var $face_app = '06d0290245ca0dad338d821792df96aa';
    var $face_url = 'https://www.facebook.com/dialog';
    var $face_redirect = 'http://www.brapci.inf.br/oauth_facebook.php';

    /* Linked in*/
    var $auth_linkedin = 1;
    var $linkedin_url = "https://www.linkedin.com/uas/oauth2/authorization";
    var $linkedin_token = "https://www.linkedin.com/uas/oauth2/accessToken";
    var $linkedin_key = '77rk2tnk7ykhoi';
    var $linkedin_key_user = '0f68b98f-4e38-4980-b631-4f64520c9c2e';
    var $linkedin_key_secret = '06fd1eff-0c5b-4d95-bb7b-681deb588919';
    var $linkedin_redirect = 'http://www.brapci.inf.br/oauth_linkedin.php';

    function __construct() {
        global $db_public;

        $db_public = 'brapci_publico.';
        parent::__construct();

        $this -> lang -> load("app", "portuguese");
        $this -> load -> helper('form_sisdoc');
        $this -> load -> library('Oauth2');
        $this -> load -> database();
        $this -> load -> helper('url');
        $this -> load -> library('session');
        $this -> load -> helper('form');
        $this -> load -> helper('form_sisdoc');        
        date_default_timezone_set('America/Sao_Paulo');
    }

    function cab($data = array()) {
        $js = array();
        $css = array();
        array_push($js, 'form_sisdoc.js');
        array_push($js, 'jquery-ui.min.js');
        array_push($js, 'jquery.maximage.js');

        array_push($css, 'asset/maximage.css');
        array_push($css, 'asset/login_maximize.css');

        $data = array();
        $data['js'] = $js;
        $data['css'] = $css;

        $data['title'] = ':: Giga Informática ::';
        $this -> load -> view('header/header', $data);
    }

    public function index() {
        redirect(base_url('index.php'));
    }

    function logout() {
        /* Salva session */
        $this -> load -> model('users');
        $this -> users -> security_logout();
        redirect(base_url('index.php/main'));
    }

    function login_local() {
        $this -> load -> model('users');

        $dd1 = $this -> input -> post('dd1');
        $dd2 = $this -> input -> post('dd2');

        if ((strlen($dd1) > 0) and (strlen($dd2) > 0)) {
            $dd1 = troca($dd1, "'", '´');
            $dd2 = troca($dd2, "'", '´');
            $ok = $this -> users -> security_login($dd1, $dd2);
        }

        if ($ok == 1) {
            redirect(base_url('index.php/main'));
        } else {
            redirect(base_url('index.php/social/login/') . '?erro=ERRO_DE_LOGIN');
        }
    }

    function login() {
        $this -> cab();
        $this -> load -> view('auth_social/login_pre', null);
        //$this -> load -> view('auth_social/login', null);
        $this -> load -> view('auth_social/login_horizontal', null);
        $this -> load -> view('header/credits', null);
    }

    public function session($provider) {

        $this -> load -> helper('url_helper');
        //facebook
        if ($provider == 'facebook') {
            //$app_id = $this -> config -> item('fb_appid');
            $app_id = $this -> face_id;
            //$app_secret = $this -> config -> item('fb_appsecret');
            $app_secret = $this -> face_app;

            $provider = $this -> oauth2 -> provider($provider, array('id' => $app_id, 'secret' => $app_secret, ));
        }
        //google
        else if ($provider == 'google') {

            //$app_id = $this -> config -> item('googleplus_appid');
            $app_id = $this -> google_key;

            //$app_secret = $this -> config -> item('googleplus_appsecret');
            $app_secret = $this -> google_key_client;
            $provider = $this -> oauth2 -> provider($provider, array('id' => $app_id, 'secret' => $app_secret, ));
        }

        //foursquare
        else if ($provider == 'foursquare') {

            $app_id = $this -> config -> item('foursquare_appid');
            $app_secret = $this -> config -> item('foursquare_appsecret');
            $provider = $this -> oauth2 -> provider($provider, array('id' => $app_id, 'secret' => $app_secret, ));
        }
        if (!$this -> input -> get('code')) {
            // By sending no options it'll come back here
            $provider -> authorize();
            redirect('social?erro=ERRO DE LOGIN');
        } else {
            // Howzit?
            try {
                $token = $provider -> access($_GET['code']);
                $user = $provider -> get_user_info($token);

                /* Ativa sessão ID */

                $ss_user = $user['name'];
                $ss_email = trim($user['email']);
                $ss_image = $user['image'];
                $ss_nome = $user['name'];
                $ss_link = $user['urls']['Facebook'];
                $ss_nivel = 0;

                $sql = "select * from users where us_email = '$ss_email' ";
                $query = $this -> db -> query($sql);
                $query = $query -> result_array();
                $data = date("Ymd");

                if (count($query) > 0) {
                    /* Atualiza quantidade de acessos */
                    $line = $query[0];
                    $ss_nivel = $line['us_nivel'];

                    $sql = "update users set us_last = '$data',
									us_acessos = (us_acessos + 1) 
								where us_email = '$ss_email' ";
                    $this -> db -> query($sql);
                } else {
                    $sql = "insert into users 
						(
							us_nome, us_email, us_cidade, 
							us_pais, us_codigo, us_link,
							us_ativo, us_nivel, us_genero, us_verificado, 
							us_cadastro, us_last
						) values (
							'$ss_nome','$ss_email','',
							'','','$ss_link',
							1,0,'',1,
							$data,$data
						)";
                    $CI = &get_instance();
                    $CI -> db -> query($sql);

                    $c = 'us';
                    $c1 = 'id_' . $c;
                    $c2 = $c . '_codigo';
                    $c3 = 7;
                    $sql = "update users set us_codigo = lpad($c1,$c3,0) where $c2='' ";
                    $rlt = $this -> db -> query($sql);
                }

                /* Salva session */
                $data = array('user' => $ss_user, 'email' => $ss_email, 'image' => $ss_image, 'nivel' => $ss_nivel);
                $this -> session -> set_userdata($data);

                if ($this -> uri -> segment(3) == 'google') {
                    //Your code stuff here
                } elseif ($this -> uri -> segment(3) == 'facebook') {
                    //your facebook stuff here

                } elseif ($this -> uri -> segment(3) == 'foursquare') {
                    // your code stuff here
                }

                $this -> session -> set_flashdata('info', $message);
                redirect('social?tabindex=s&status=sucess');

            } catch (OAuth2_Exception $e) {
                show_error('That didnt work: ' . $e);
            }

        }
    }

    function myaccount() {
        $id = $_SESSION['id'];
        $this -> load -> model('users');

        $this -> cab();
        $data['title'] = '';
        $data['content'] = $this -> users -> my_account($id);
        $this -> load -> view('content', $data);
    }

}
