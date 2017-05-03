<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24/07/16
 * Time: 18:31
 */

namespace Lib;

class Email
{
    private $destinatario = "dezmedidas@aureliocasoni.xyz";
    private $senha = "isHY5HzRbA";
    private $remetente;
    private $titulo;
    private $corpo;
    private $phpMailer;
    private $nome;

    /**
     * Email constructor.
     * @param $remetente
     * @param $titulo
     * @param $corpo
     */
    public function __construct($remetente, $nome, $titulo, $corpo)
    {
        $this->remetente = $remetente;
        $this->titulo = $titulo;
        $this->nome = $nome;
        $this->corpo = $corpo;
        $this->phpMailer = new PHPMailer();
        $this->phpMailer->isSMTP();
        $this->phpMailer->CharSet = 'UTF-8';
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->phpMailer->SMTPDebug = 0;
        //Ask for HTML-friendly debug output
        $this->phpMailer->Debugoutput = 'html';
        //$this->phpMailer->i
        //Set the hostname of the mail server
        $this->phpMailer->Host = 'mx1.hostinger.com.br';
        // use $this->phpMailer->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->phpMailer->Port = 587;
        //Set the encryption system to use - ssl (deprecated) or tls
        $this->phpMailer->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $this->phpMailer->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $this->phpMailer->Username = $this->destinatario;
        //Password to use for SMTP authentication
        $this->phpMailer->Password = $this->senha;
        //Set who the message is to be sent from
        $this->phpMailer->setFrom($this->destinatario, 'Administrador - 10 Medidas Sim');
        //Set who the message is to be sent to
        $this->phpMailer->addAddress($this->remetente, $this->nome);
        //Set the subject line
        $this->phpMailer->Subject = $this->titulo;
    }

    //esta funcao faz enviar um e-mail para o destinatario informado com os dados pedidos de contato
    //retorna true se foi enviado com êxito, retorna false se foi enviado com erros
    function enviar(){
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $this->phpMailer->msgHTML($this->corpo);
        //Replace the plain text body with one created manually
        $this->phpMailer->AltBody = str_replace("<br />", "\n", $this->corpo);
        //Attach an image file
        //$this->phpMailer->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        if (!$this->phpMailer->send()) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function getMessage()
    {
        return $this->phpMailer->ErrorInfo;
    }

}
