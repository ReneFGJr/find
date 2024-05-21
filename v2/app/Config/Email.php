<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public $userAgent = 'CodeIgniter';
    public $mailPath = '/usr/sbin/sendmail';
    public $SMTPKeepAlive = false;
    public $wrapChars = 76;
    public $charset = 'UTF-8';
    public $validate = false;
    public $priority = 3;
    public $CRLF = "\r\n";
    public $BCCBatchMode = false;
    public $BCCBatchSize = 200;

    public $DSN = false;

    public $fromEmail = '00282381@ufrgs.br';
    public $fromName  = 'FIND';
    public $recipients = '';

    public $SMTPHost = 'smtp.ufrgs.br';
    public $SMTPUser = '00282381@ufrgs.br';
    public $SMTPPass = 'aNDRE@2023';
    public $SMTPPort = 587;  // Porta SMTP (por exemplo, 587 para TLS ou 465 para SSL)
    public $SMTPCrypto = 'tls';  // Pode ser 'ssl' ou 'tls'

    public $protocol = 'smtp';
    public $mailType = 'html';  // Pode ser 'text' ou 'html'
    public $SMTPTimeout = 5;
    public $wordWrap = true;
    public $newline = "\r\n";  // Requerido para alguns servidores de e-mail
}
