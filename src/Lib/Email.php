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
    private static $filePathConfig = "../assets/configEmail.ini";
    private $destinatario;
    private $senha;
    private $remetente;
    private $titulo;
    private $corpo;
    private $phpMailer;
    private $nome;

    /**
     * Representa um e-mail no sistema
     * @param string $destinatario
     * @param string $nome
     * @param string $titulo
     * @param string $corpo
     */
    public function __construct(string $destinatario, string $nome, string $titulo, string $corpo)
    {
        $this->destinatario = $destinatario;
        $this->titulo = $titulo;
        $this->nome = $nome;
        $this->corpo = $corpo;
        try{
            $this->phpMailer = $this->readConfiguration();
        } catch (Exception $ex) {
            throw $ex;
        }
        //Set who the message is to be sent to
        $this->phpMailer->addAddress($this->destinatario, $this->nome);
        //Set the subject line
        $this->phpMailer->Subject = $this->titulo;
    }

    /**
     * Esta funcao faz enviar um e-mail para o destinatario informado com os dados pedidos de contato
     * @return boolean Retorna TRUE se foi enviado com êxito, retorna FALSE se foi enviado com erros
     */
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
    
    /**
     * Retorna a mensagem de erro, caso a operação de envio falhe
     * @return string A mensagem de erro
     */
    public function getMessage()
    {
        return $this->phpMailer->ErrorInfo;
    }
    
    /**
     * Seta o caminho para buscar as configurações de e-mail
     * @param string $newFilePath O novo caminho a ser salvo
     */
    public static function setFilePathConfig(string $newFilePath) {
        self::$filePathConfig = $newFilePath;
    }
    
    /**
     * Lê o arquivo de configurações e prepara o PHPMailer
     * @return object 
     * @throws \RuntimeException Caso o arquivo não seja encontrado
     */
    private function readConfiguration(){
        $data = parse_ini_file(self::$filePathConfig);
        if ($data === FALSE){
            throw new \RuntimeException("Arquivo ini de configurações de e-mail não foi encontrado!");
        }
        $mailer = new \PHPMailer();
        $mailer->isSMTP();
        $mailer->CharSet = 'UTF-8';
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mailer->SMTPDebug = 0;
        //Ask for HTML-friendly debug output
        $mailer->Debugoutput = 'html';
        //$this->phpMailer->i
        //Set the hostname of the mail server
        $mailer->Host = $data["host"];
        // use $this->phpMailer->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mailer->Port = $data["porta"];
        //Set the encryption system to use - ssl (deprecated) or tls
        $mailer->SMTPSecure = $data["criptografia"];
        //Whether to use SMTP authentication
        $mailer->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mailer->Username = $data["username"];
        //Password to use for SMTP authentication
        $mailer->Password = $data["senha"];
        //Set who the message is to be sent from
        $mailer->setFrom($data["remetente"], $data["identificacao"]);
        return $mailer;
    }
}
