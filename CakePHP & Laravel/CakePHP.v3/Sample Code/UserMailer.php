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



---------
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
