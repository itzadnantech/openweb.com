<?php


class daily_usage_log extends CI_Model
{
    protected $cron_id = null;

    public function start() {
        $this->cron_id = time();

        $this->log('start_cron');
    }

    public function end() {
        $this->log('end_cron');
        $this->cron_id = null;
    }

    public function error($error) {
        $this->log('error', $error);
    }

    public function log($action, $comment = '') {
        $this->db->insert('daily_usage_log', [
            'cron_id' => $this->cron_id,
            'action' => $action,
            'comment' =>$comment
        ]);
    }
}

