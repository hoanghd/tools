namespace App\Mailer;

use Cake\Mailer\Mailer;

class EntriesMailer extends Mailer{
    public function created($entry=null){
        $this
            ->to('hoanghd.it@gmail.com')
            ->subject('【コーティング一括見積りESCOAT】受付完了のご連絡')
            ->viewVars([
                'entry'=> $entry
            ])
            ->template('entries/created', null)
            ->emailFormat('text');
    }
}
---------------
use Cake\Mailer\MailerAwareTrait;
        
class DefaultController extends AppController
{
    use MailerAwareTrait;
    
    public function index()
    {
        $this->getMailer('Entries')
                ->send('created', [1]);
    }
}
---------------
app.php
---------------
'Email' => [
    'default' => [
        'transport' => 'gmail',
        'from' => 'no-return@coating.kurumaerabi.com',
        'charset' => 'ISO-2022-JP',
        //'headerCharset' => 'utf-8',
    ],
],

'gmail'=> [
    'host' => 'ssl://smtp.gmail.com',
    'port' => 465,
    'username' => 'mikoashi@gmail.com',
    'password' => '----',
    'className' => 'Smtp',
    'log' => true,
    'context' => [
      'ssl' => [
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
      ]
    ]
],
