<?php


class lte_usage_stats_model extends CI_Model
{
    private $settings = [];

    private function getSettings($force = false) {
        if ($force || $this->needUpdate()) {
            $query = $this->db->get('lte_usage_stats_settings');
            $result = $query->result_array('array');
            foreach ($result as $row) {
                $this->settings[$row['slug']] = $row['display'];
            }
        }

        return $this->settings;
    }

    private function needUpdate() {
        return empty($this->settings);
    }

    public function toDisplay($slug, $force = false) {
        $settings = $this->getSettings();

        return $settings[$slug] == 1;
    }

    public function save($new) {
        $settings = $this->getSettings();

        $remove = [];
        $add = [];

        foreach ($settings as $slug => $item) {
            if (in_array($slug, $new)) {
                if ($item == 0) {
                    $add[] = $slug;
                }
            } else {
                if ($item == 1) {
                    $remove[] = $slug;
                }
            }
        }

        if (!empty($remove)) {
            $this->db->where_in('slug', $remove);
            $this->db->update('lte_usage_stats_settings', [
                'display' => 0
            ]);
        }

        if (!empty($add)) {
            $this->db->where_in('slug', $add);
            $this->db->update('lte_usage_stats_settings', [
                'display' => 1
            ]);
        }
    }

    public function getLteUsageStats() {
        $this->load->model('isdsl_model');
        $this->load->model('network_api_handler_model');

        $realm = 'openwebmobile.co.za';

        $users = $this->network_api_handler_model->getLTEUsernames($realm);
        $userStats = [] ;
        $userDiv = floor(count($users) / 100) + 1;

        for($i = 0; $i < $userDiv; $i++) {
            $userList = array_slice($users, $i*100, 100);
            $userStats = array_merge($userStats, $this->isdsl_model->getLteUsagesList($userList));
        }

        foreach ($userStats as $user => $data) {

            $totalData = 0;

            if(!isset($data['Error'])) {
                foreach ($data['Packages'] as $package) {
                    $totalData += ($package["Total Data"] - $package['Remaining Data']);
                }
            }

            $inser = [
                'username' => $user,
                'usage' => $totalData
            ];

            $userDB = $this->db
                ->where('username', $user)
                ->get('lte_usage_stat')
                ->result_array();

            if(isset($userDB[0]['id'])) {
                $this->db->where('id', $userDB[0]['id'])->update('lte_usage_stat', $inser);
                continue;
            }

            $this->db->insert('lte_usage_stat', $inser);
        }

        //Update date
        $this->db->where('action', 'lte_updated')->update('system_param', ['toggle' => date('d-m-Y H:i:s')]);
    }
}

