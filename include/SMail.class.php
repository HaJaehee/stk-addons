<?php
/* copyright 2009 Lucas Baudin <xapantu@gmail.com>                 
             2014 Daniel Butum <danibutum at gmail dot com>
 This file is part of stkaddons.                                 
                                                                              
 stkaddons is free software: you can redistribute it and/or      
 modify it under the terms of the GNU General Public License as published by  
 the Free Software Foundation, either version 3 of the License, or (at your   
 option) any later version.                                                   
                                                                              
 stkaddons is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for    
 more details.                                                                
                                                                              
 You should have received a copy of the GNU General Public License along with 
 stkaddons.  If not, see <http://www.gnu.org/licenses/>.
*/

// Include PEAR::Mail
require_once('Mail.php');

/**
 * Class SMail
 */
class SMail
{
    /**
     * @var Mail
     */
    private $factory;

    /**
     * @var array
     */
    private $headers;

    /**
     * The constructor
     */
    public function __construct()
    {
        if (MAIL_METHOD == 'smtp')
        {
            @$this->factory = Mail::factory(
                MAIL_METHOD,
                array(
                    'host'     => SMTP_HOST,
                    'port'     => SMTP_PORT,
                    'auth'     => SMTP_AUTH,
                    'username' => SMTP_USER,
                    'password' => SMTP_PASS
                )
            );
        }
        else
        {
            @$this->factory = Mail::factory(
                MAIL_METHOD,
                array(
                    'sendmail_path' => SENDMAIL_PATH,
                    'sendmail_args' => SENDMAIL_ARGS
                )
            );
        }
        $this->headers = array(
            'From'                      => '"STK-Addons Administrator" <' . ConfigManager::getConfig('admin_email') . '>',
            'Content-Type'              => 'text/plain; charset="UTF-8"',
            'Content-Transfer-Encoding' => '8bit'
        );
    }

    /**
     * @param string $email
     * @param int $userid
     * @param string $username
     * @param string $ver_code
     * @param string $ver_page
     *
     * @throws Exception
     */
    public function newAccountNotification($email, $userid, $username, $ver_code, $ver_page)
    {
        $message = "Thank you for registering an account on the SuperTuxKart Add-Ons Manager.\n" .
            "Please go to " . SITE_ROOT . "$ver_page?action=valid&num=$ver_code&user=$userid to activate your account.\n\n" .
            "Username: $username";
        $subject = "New Account at " . $_SERVER["SERVER_NAME"];

        $this->headers['To'] = $email;
        $this->headers['Subject'] = $subject;
        $result = @$this->factory->send($email, $this->headers, $message);
        if (@PEAR::isError($result))
        {
            throw new Exception($result->getMessage());
        }
    }

    /**
     * @param string $email
     * @param int $userid
     * @param string $username
     * @param string $ver_code
     * @param string $ver_page
     *
     * @throws Exception
     */
    public function passwordResetNotification($email, $userid, $username, $ver_code, $ver_page)
    {
        $message = "You have requested to reset your password on the SuperTuxKart Add-Ons Manager.\n" .
            "Please go to http://{$_SERVER["SERVER_NAME"]}/$ver_page?action=valid&num=$ver_code&user=$userid to reset your password.\n\n" .
            "Username: $username";
        $subject = "Reset Password on " . $_SERVER["SERVER_NAME"];

        $this->headers['To'] = $email;
        $this->headers['Subject'] = $subject;
        $result = @$this->factory->send($email, $this->headers, $message);
        if (@PEAR::isError($result))
        {
            throw new Exception($result->getMessage());
        }
    }

    /**
     * @param string $email
     * @param string $addon_id
     * @param string $notes
     *
     * @throws Exception
     */
    public function addonNoteNotification($email, $addon_id, $notes)
    {
        $addon_name = Addon::getNameByID($addon_id);
        $message =
            "A moderator has left a note concerning your add-on, '$addon_name.' The notes saved for each revision of this add-on are shown below.\n\n";
        $message .= $notes;
        $subject = "New message for add-on '$addon_name'";

        $this->headers['To'] = $email;
        $this->headers['Subject'] = $subject;
        $result = @$this->factory->send($email, $this->headers, $message);
        if (@PEAR::isError($result))
        {
            throw new Exception($result->getMessage());
        }
    }
}
