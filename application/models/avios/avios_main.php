<?php
//  handler all buisness processes
class Avios_main extends CI_Model 
{

	private $aviosVirtualStorage = null;
	private $aviosPhysicalStorage = null;
	private $aviosFtpHelperStorage = null;
	private $aviosEncodeHelperStorage = null;
	public $billingCodes = [
        "OPNZAFIBRT" => 'Fibre Router',
        'OPNZAB1000' => 'Bolt On Product Offer 1000 Avios',
        'OPNZABO250' => 'Bolt On Product offer 250 Avios',
        'OPNZABO500' => 'Bolt On Product offer 500 Avios',
        'OPNZAFIBMR' => 'Fibre Monthly Rental',
        'OPNZALINRT' => 'ADSL Line Rental',
        'OPNZAMMDPS' => 'Monthly mobile data package subscription',
        'OPNZARO050' => 'Reseller Offer on billed data above R50k',
        'OPNZARO100' => 'Reseller Offer on billed data above R100k',
        'OPNZAUBAMR' => 'Uncapped ADSL Monthly Rental',
        'OPNZAUBFMR' => 'Uncapped Fibre Monthly Rental'
    ];
	public $bonusBillingCode = "OPNZABONUS";

    public function __construct()
    {
        $this->load->model('avios/avios_physical');
        $this->load->model('avios/avios_logs');
        $this->load->model('membership_model');
    }
    // save new virtual award type

	// load virtual award type

	// attach virtual award

	// convert virtual award to physical (API side)

	// convert virtual award to physical (client side)

	// reports 

    //Make file from gathered data
    public function dailyAwardFile() {

        $data = $this->awardDataGathering();

        if(empty($data)) {
            return false;
        }

        if($this->avios_physical->createFile($data)) {
            $this->avios_logs->cleanPrepareTable();
            return true;
        }
        return false;
    }

    //parse incomig files from Avios
    public function parseNewFiles() {

        $this->load->model("message_model");

        $path = FCPATH."application/avios/outbound/";
        $files = scandir($path);

        $files = $this->clearifyFileList($files, $path);

        if(empty($files)) {
            return false;
        }

        $oldFiles = $this->avios_logs->getAnswerFiles();

        foreach ($files as $file) {

            if(in_array($file, $oldFiles)) {
                continue;
            }

            $parser = $this->avios_physical->parseConfirmationFile($file);
            if($parser === true) {
                $this->avios_logs->addNewAnswerFile($file, "added");
            }
            else {
                $this->avios_logs->addNewAnswerFile($file, $parser);
                $message = "Can not parse file ".$file." Error received ".$parser;
                $this->message_model->send_email("avios_error@openweb.co.za", "sergey.gerashchenko@lamp-dev.com", "Parse Avios award file error", $message, array());
                $this->message_model->send_email("avios_error@openweb.co.za", "ceo@openweb.email", "Parse Avios award file error", $message, array());
            }
        }
    }

    public function clearifyFileList($files, $path) {

        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            $res = preg_split('/[.]/', $files[$i]);

            if (strlen($res[0]) !== 26 || strlen($res[1]) !== 3 || is_dir($path.$files[$i])) {
                unset($files[$i]);
            }
        }
        var_dump("We have result: ", $files);
        return $files;
    }

    //write to DB new award into prepare table
    public function giveAviosAward($award_data) {

        if(!isset($award_data['bonus_billing_code'])) {
            $award_data['bonus_billing_code'] = "";
        }

        if(!isset( $award_data['bonus-points'])) {
            $award_data['bonus-points'] = 0;
        }

        $res = $this->avios_logs->addPrepareAward(
            $award_data['order_id'],
            $award_data['user_id'],
            $award_data['points'],
            $award_data['bonus-points'],
            //$award_data['loyalty-programme-member-id'],
            $award_data['billing-code'],
            //$award_data['loyalty-programme-name'],
            $award_data['bonus-billing-code']
        );

        return $res;
    }

    //Process prepare table and make array() of data for each user
    public function awardDataGathering() {

        $allData = $this->avios_logs->getPrepareAwards();
        $output = [];

        foreach ($allData as $item) {

            if(!$this->getLoyaltyData($item)) {
                continue;
            }

            if (in_array($item['user_id'], array_keys($output))) {
                $output[$item['user_id']]['order_id'] .= " ".$item['order_id'];//Thinking about it
                $output[$item['user_id']]['points'] += $item['points'];
                $output[$item['user_id']]['bonus-points'] += $item['bonus-points'];
                continue;
            }

            $output[$item['user_id']] = $item;

        }

        $output = array_values($output);

        return $output;
    }

    public function getLoyaltyData(&$data_set) {

        $loyalty_member = $this->membership_model->getLoyaltyData($data_set['user_id']);

        if(!$loyalty_member) {
            $this->avios_logs->updatePrepareStatusByUser("no_id", $data_set['user_id']);
            return false;
        }

        $data_set['loyalty-programme-member-id'] = $loyalty_member[0];
        $data_set['loyalty-programme-name'] = $loyalty_member[1];

        return $data_set;
    }

    public function findRule($rules, $code) {

        foreach ($rules as $rule) {

            if($rule['billing_code'] == $code) {

                if($rule['mothly'] == 1) {
                    return [1, $rule['m_rule']];
                } else {
                    return [0, $rule['once_points']];
                }

            }
        }
    }
}