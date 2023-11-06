<?php

namespace app\admin\unit;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer as PHPMailerClass;

class PhpMailer
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailerClass(true);

        // 配置 SMTP 设置
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.example.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'your_username';
        $this->mailer->Password = 'your_password';
        $this->mailer->SMTPSecure = PHPMailerClass::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;

        // 设置发件人
        $this->mailer->setFrom('from@example.com', 'Your Name');
    }

    /**
     * @Desc:发送邮件
     * @param $to string 收件人邮箱地址
     * @param $subject string 邮件主题
     * @param $message string 邮件内容
     * @return bool
     * @author: hzc
     * @Time: 2023/8/11 14:27
     */
    public function sendEmail($to, $subject, $message)
    {
        try {
            // 设置收件人
            $this->mailer->addAddress($to);

            // 邮件内容设置
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;
            //$this->phpMailer->AltBody = '这是非HTML邮件客户端的纯文本正文';

            //名称是可选的
//            $mail->addAddress('ellen@example.com');
//
//            $mail->addReplyTo('info@example.com', 'Information');
//
//            $mail->addCC('cc@example.com');
//
//            $mail->addBCC('bcc@example.com');

//            //附件
//
//            //添加附件
//            $mail->addAttachment('/var/tmp/file.tar.gz');
//
//            //可选名称
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

            // 发送邮件
            $this->mailer->send();

            return true;
        } catch (PHPMailerException $e) {
            return false;
        }
    }
}