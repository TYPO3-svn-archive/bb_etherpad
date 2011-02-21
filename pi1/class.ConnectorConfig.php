<?php
/* ----------------------------------------------------------------------------
* Mark Schmale <ma.schmale@googlemail.com>
* ----------------------------------------------------------------------------
* Solange Sie diesen Vermerk nicht entfernen, können Sie mit der Datei machen,
* was Sie möchten. Wenn wir uns eines Tages treffen und Sie denken, die Datei
* ist es wert, können Sie mir dafür ein Bier ausgeben.
* ----------------------------------------------------------------------------
*/

class ConnectorConfig
{
    /**
* hostname of the used etherpad
* @var String
*/
    public $padHost = 'padcms.padserver.net';

    /**
* username (email) for authentication
* @var String
*/
    public $padUser = 'padcms@masch.it';

    /**
* the password
* @var String
*/
    public $padPass = 'password';
    
    public $cookieJar = '/tmp/hmm.cookies.nomnom';

    public $debug = false;
}