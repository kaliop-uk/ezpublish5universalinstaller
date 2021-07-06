<?php

namespace Kaliop\eZP5UI\Common;

use PDO;

/**
 * @todo add cleanup of: classes and workflows and roles in 'draft' status, same as legacy 'flatten.php' script does
 * @todo add optional cleanup of archived content versions
 * @todo allow end user to pass in a list of extra tables to empty
 */
class DatabaseHandler extends Logger
{
    //protected $dsn;
    //protected $user;
    //protected $password;
    protected $db;

    protected $cleanupTables = array(
        'ezuservisit',
        'ezpreferences',
        'ezsearch_word',
        'ezsearch_object_word_link',
        'ezpending_actions',
        'ezcontentbrowserecent',
        'eznotificationevent',
        'ezworkflow_process',
    );

    public function __construct($dsn, $user = null, $password = null, array $options = array()) {
        $this->db = new PDO($dsn, $user, $password, $options);
    }

    public function cleanup() {
        foreach ($this->cleanupTables as $table) {
            // NB: we might use DROP STORAGE, but we expect the db not to be too big when this script is run...
            $count = $this->db->exec('DELETE FROM ' . $table);
            $this->info("Deleted $count rows from table '$table'");
        }
    }
}
