<?php

namespace app\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

/*klasa koja salje email sa linkom za reset lozinke */
class Mail_sender{
    
    private static $mail = null;
    const EMAIL_SEND = 'enter email';
    const EMAIL_PASS = 'email password';

    public static function posalji_mejl($primalac, $naslov, $sadrzaj, $hash, $time, $id, $tip,$poruka){

        if(self::$mail == null){
            self::$mail = new PHPMailer(true);
        }

        $mail = new PHPMailer(true);
            try {
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
    
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.office365.com';
                $mail->Port       = 587;
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                $mail->SMTPAuth   = true;
                $mail->Username = self::EMAIL_SEND;
                $mail->Password = self::EMAIL_PASS;
                $mail->SetFrom(self::EMAIL_SEND, 'Reset');
                $mail->addAddress($primalac, 'ToEmail');
                $mail->IsHTML(true);
                $mail->Subject = $naslov;
                $sadrzaj = '';
                $link = "token=${hash}&time=${time}&id=${id}";

                if($tip == 'preduzece'){
                    $sadrzaj = '<a href="/resetPreduzece?'.$link.'"> link </a>';
                }else{
                    $link = "token=${hash}&time=${time}&id=${id}";

                    $sadrzaj = '<a href="/reset?'.$link.'"> link </a>';

                }
                $mail->Body = $sadrzaj;

                if(!$mail->send()) {
                    echo 'Message could not be sent.';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                } 
               
                echo $poruka;
            } catch (Exception $e) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
    }
}