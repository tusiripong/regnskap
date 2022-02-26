<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2013, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Content context controller
 *
 * The controller which displays the homepage of the Content context in Bonfire site.
 *
 * @package    Bonfire
 * @subpackage Controllers
 * @category   Controllers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/file_helpers.html
 *
 */
class Content extends Admin_Controller
{


	/**
	 * Controller constructor sets the Title and Permissions
	 *
	 */
	public function __construct()
	{
		parent::__construct();
        $this->load->helper('application');
		$this->load->library('Template');
		$this->load->library('Assets');
		$this->lang->load('application');
        $this->load->helper('file');
        $this->load->helper('download');
		$this->load->library('events');
                $this->load->model('projects/projects_model');
                $this->load->model('invoices/invoices_model');
                $this->load->model('banktransactions/banktransactions_model');
                $this->load->helper("url");


        $this->load->library('installer_lib');
        $this->load->dbutil();
        if (! $this->installer_lib->is_installed()) {
            $ci =& get_instance();
            $ci->hooks->enabled = false;
            redirect('install');
        }
		Template::set('toolbar_title', 'Content');

		$this->auth->restrict('Site.Content.View');
	}//end __construct()

	//--------------------------------------------------------------------

    //public $url_json_main_data = "http://financial_tor.totoit.co.th/assets/main_data.json";
    public $url_json_main_data = "http://finans.k12.no/assets/main_data.json";

	/**
	 * Displays the initial page of the Content context
	 *
	 * @return void
	 */
	public function index()
	{
                $modules = Modules::list_modules(true);
                $configs = array();

                foreach ($modules as $module) {
                    $configs[$module] = Modules::config($module);
                    if (! isset($configs[$module]['name'])) {
                        $configs[$module]['name'] = ucwords($module);
                    } elseif (strpos($configs[$module]['name'], 'lang:') === 0) {
                        // If the name is configured, check to see if it is a lang entry
                        // and, if it is, pull it from the application_lang file.
                        $configs[$module]['name'] = lang(str_replace('lang:', '', $configs[$module]['name']));
                    }
                }
                
                //echo "<pre>";print_r($configs);echo "</pre>";
                // if(!empty($sorting)){
                //     $this->db->where('project_name like "'.$sorting.'%"');
                // }
                //$this->db->order_by('project_name','asc');
                $projects = $this->db->get_where('bf_project',array('deleted'=>0,'use_bank'=>0));                
                //$list_projects = (!empty($p_invoice))?$this->projects_model->get_list_project_special("","","","invoice",false,$sorting,1):$this->projects_model->get_list_project_special("","","","all",false,$sorting,1);
                
                //$project_session = ($this->session->has_userdata($name))?$_SESSION[$name]:null;
                // $name = 'setting_project_defualt_'.$this->session->userdata('user_id');
                // $query_setting = $this->db->get_where('bf_settings',array('name'=>$name));
                // $data = null;
                // $data['list_data_unlock'] = null;
                // foreach($list_projects->result() as $item):
                //     $bank_transaction = $this->banktransactions_model->get_banktransaction_listALL($item->project_original_id,1);
                //     $invoice_betail = $this->invoices_model->get_invoice_unlock($item->project_original_id,1,0);
                //     $invoice_Ubetail = $this->invoices_model->get_invoice_unlock($item->project_original_id,0,0);
                //     $transaction_unlock = $bank_transaction->num_rows()+$invoice_betail->num_rows();
                //     $data['list_data_unlock'][$item->project_original_id]['transaction_unlock'] = $transaction_unlock;
                //     $data['list_data_unlock'][$item->project_original_id]['invoice_unlock'] = $invoice_Ubetail->num_rows();
                // endforeach;

                //check rows data financial and regnskap
                
                $list_avd = $this->db->get('bf_avd');
                $list_supplier = $this->db->get('bf_supplier');
                $list_bank_account = $this->db->get('bf_bankaccount');
                $data_financial = json_decode(file_get_contents($this->url_json_main_data));

                //end check rows data financial and regnskap
                Template::set('new_rows_project',count($data_financial->projects)-$projects->num_rows());
                Template::set('new_rows_avd',count($data_financial->avds)-$list_avd->num_rows());
                Template::set('new_rows_supplier',count($data_financial->suppliers)-$list_supplier->num_rows());
                Template::set('new_rows_bankaccount',count($data_financial->bankaccounts)-$list_bank_account->num_rows());
                //Template::set('setting_user',$query_setting->result_array());             
                //Template::set('listprojects',$list_projects->result());
                //Template::set('list_data_unlock',$data['list_data_unlock']);
                Template::set('listmodules',$configs);
		Template::set_view('admin/content/index');
		Template::render();
	}//end index()


	//--------------------------------------------------------------------
        public function set_project_defualt(){
            $project_id = $this->input->post('project_id');
            $user_id =  $this->session->userdata('user_id');

            $name = "setting_project_defualt_".$user_id;
            $value = $project_id;
            // if($this->session->has_userdata($name)){
            //     $this->session->set_userdata(array($name=>$value));
            // }else{
            //     $this->session->set_userdata($name, $value);
            // }
            $query = $this->db->get_where('bf_settings',array('name'=>$name));
            if($query->num_rows() == 0){
                $this->db->insert('bf_settings',array('name'=>$name,'module'=>'core','value'=>$value));
            }else{
                $this->db->update('bf_settings',array('module'=>'core','value'=>$value),array('name'=>$name));
            }

        }
        

}//end class