<?php
namespace Application\Service;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class MailService
{
    /**
     * @var array
     */
    private $settings;

    /**
     * MailService constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->setSettings($settings);
    }

    /**
     * @param string $email
     * @param string $htmlBody
     * @param string $subject
     * @return array
     * @throws \Zend\Mime\Exception\InvalidArgumentException
     * @throws \Zend\Mail\Exception\InvalidArgumentException
     */
    public function sendMail($email, $htmlBody, $subject = 'Автоматическое оповещение')
    {
        $success = true;
        $error = [];

        $html = new MimePart($htmlBody);
        $html->setType(Mime::TYPE_HTML);
        $body = new MimeMessage();
        $body->setParts([$html]);

        $message = new Message();
        $message->addFrom($this->getSettings('username'), $this->getSettings('site'))
                ->addTo($email)
                ->setSubject($subject)
                ->setEncoding('UTF-8')
                ->setBody($body);

        $message->getHeaders()->get('content-type')->setType(Mime::TYPE_HTML);

        $transport = new SmtpTransport();
        $transport->setOptions(new SmtpOptions($this->getConnectorFromSettings()));

        try {
            $transport->send($message);
        }
        catch(\Exception $ex) {
            $success = false;
            $error[] = $ex->getMessage();
        }

        return [
            'success'   => $success,
            'error'     => $error
        ];
    }

    /**
     * @param string|null $param
     * @return array|string
     */
    public function getSettings($param = null)
    {
        if ($param !== null) {
            return $this->settings[$param];
        }
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return MailService
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    protected function getConnectorFromSettings()
    {
        return [
            'name'  => $this->getSettings('site'),
            'host'  => $this->getSettings('host'),
            'port'  => $this->getSettings('port'),
            'connection_class'  => 'login',
            'connection_config' => [
                'username'  => $this->getSettings('username'),
                'password'  => $this->getSettings('password'),
                'ssl'       => $this->getSettings('encryption'),
            ],
        ];
    }
}