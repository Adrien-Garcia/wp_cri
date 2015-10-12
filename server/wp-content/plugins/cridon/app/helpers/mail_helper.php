<?php

/**
 * Class MailHelper
 *
 * @author eTech
 */
class MailHelper extends MvcHelper
{
    /**
     * @params string $to Can be an array if multiple recipients
     * @params string $subject
     * @params string $message
     * @params array  $options
     */
    public function sendMail($to, $subject, $message, $options = array())
    {
        $defaultOptions = array(
            'attachments' => array(),
            'from'        => array(),
            'Cc'          => array(),
            'isHtml'      => true
        );
        $options        = array_merge($defaultOptions, $options);
        $headers        = array();
        if ($options['isHtml']) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        }
        $name = '';
        $mail = $options['from']['mail'];
        if (isset( $options['from']['name'] )) {
            $name = $options['from']['name'];
            $mail = ' <' . $options['from']['mail'] . '>';
        }
        $headers[] = 'From: ' . $name . $mail;
        if (count($options['Cc']) > 0) {
            foreach ($options['Cc'] as $cc) {
                $name = '';
                $mail = $cc['mail'];
                if (isset( $cc[' name'] )) {
                    $name = $cc[' name'];
                    $mail = ' <' . $cc['mail'] . '>';
                }
                $headers[] = 'Cc: ' . $name . $mail;
            }
        }
        if (count($options['Bcc']) > 0) {
            foreach ($options['Bcc'] as $bcc) {
                $name = '';
                $mail = $bcc['mail'];
                if (isset( $bcc[' name'] )) {
                    $name = $bcc[' name'];
                    $mail = ' <' . $bcc['mail'] . '>';
                }
                $headers[] = 'Bcc: ' . $name . $mail;
            }
        }
        $attachments = $options['attachments'];

        if (count($attachments) > 0) {
            return wp_mail($to, $subject, $message, $headers, $attachments);
        } else {
            return wp_mail($to, $subject, $message, $headers);
        }
    }
}