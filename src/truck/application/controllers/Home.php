<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Bonfire
 *
 * An open source project to allow developers to jumpstart their development of
 * CodeIgniter applications.
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2014, Bonfire Dev Team
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

/**
 * Home controller
 *
 * The base controller which displays the homepage of the Bonfire site.
 *
 * @package    Bonfire
 * @subpackage Controllers
 * @category   Controllers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/file_helpers.html
 *
 */
class Home extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('application');
        $this->lang->load('application');
        $this->load->library('Template');
        $this->load->library('Assets');
        $this->load->library('events');
        $this->load->model('projects/projects_model');
        $this->load->model('invoices/invoices_model');
        $this->load->model('banktransactions/banktransactions_model');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->load->helper('mysqli');


        $this->config->load('s3', TRUE);
        $s3_config = $this->config->item('s3');
        $this->bucket_name = $s3_config['bucket_name'];
        $this->folder_name = $s3_config['folder_name'];
        $this->s3_url = $s3_config['s3_url'];

        $this->load->library('installer_lib');
        $this->load->dbutil();
        if (!$this->installer_lib->is_installed()) {
            $ci = &get_instance();
            $ci->hooks->enabled = false;
            redirect('install');
        }

        // Make the requested page var available, since
        // we're not extending from a Bonfire controller
        // and it's not done for us.
        $this->requested_page = isset($_SESSION['requested_page']) ? $_SESSION['requested_page'] : null;
    }

    //public $server_path_sql = "C:/Tony/Websites/financial_UI/financial_tor/trunk/public/assets/DBbackup/";
    public $server_path_sql = "C:\WebSites\All_Projects_financial/finans.k12.no/trunk\public\assets\DBbackup/";
    public $data_financial = "http://finans.k12.no/assets/main_data.json";

    //--------------------------------------------------------------------

    /**
     * Displays the homepage of the Bonfire app
     *
     * @return void
     */
    public function index()
    {
        $this->load->library('users/auth');
        $this->set_current_user();

        //                $modules = Modules::list_modules(true);
        //                $configs = array();
        //
        //                foreach ($modules as $module) {
        //                    $configs[$module] = Modules::config($module);
        //                    if (! isset($configs[$module]['name'])) {
        //                        $configs[$module]['name'] = ucwords($module);
        //                    } elseif (strpos($configs[$module]['name'], 'lang:') === 0) {
        //                        // If the name is configured, check to see if it is a lang entry
        //                        // and, if it is, pull it from the application_lang file.
        //                        $configs[$module]['name'] = lang(str_replace('lang:', '', $configs[$module]['name']));
        //                    }
        //                }
        //                
        //                echo "<pre>";print_r($configs);echo "</pre>";

        Template::render();
    } //end index()


    public function index_form()
    {
        $p_invoice = ($this->input->get('p_invoice')) ? $this->input->get('p_invoice') : null;
        $sorting = ($this->input->get('sorting')) ? $this->input->get('sorting') : null;
        $data = null;
        $list_projects = (!empty($p_invoice)) ? $this->projects_model->get_list_project_special("", "", "", "invoice", false, $sorting, 1) : $this->projects_model->get_list_project_special("", "", "", "all", false, $sorting, 1);
        $data['listprojects'] = $list_projects->result();
        $data['list_data_unlock'] = null;

        $name = 'setting_project_defualt_' . $this->session->userdata('user_id');
        $query_setting = $this->db->get_where('bf_settings', array('name' => $name));
        $data['setting_user'] = $query_setting->result_array();

        // foreach($list_projects->result() as $item):
        //     $bank_transaction = $this->banktransactions_model->get_banktransaction_listALL($item->project_original_id,1);
        //     $invoice_betail = $this->invoices_model->get_invoice_unlock($item->project_original_id,1,0);
        //     $invoice_Ubetail = $this->invoices_model->get_invoice_unlock($item->project_original_id,0,0);
        //     $transaction_unlock = $bank_transaction->num_rows()+$invoice_betail->num_rows();
        //     $data['list_data_unlock'][$item->project_original_id]['transaction_unlock'] = $transaction_unlock;
        //     $data['list_data_unlock'][$item->project_original_id]['invoice_unlock'] = $invoice_Ubetail->num_rows();
        // endforeach;    
        //print_r($data);exit();
        //Template::set_view('admin/content/index_form');
        $this->load->view('admin/content/index_form', $data);
    }

    public function popup_info_project()
    {
        $project_id = $this->input->get('project_id');
        $data = array();

        $data['project_name'] = $this->projects_model->get_project_field($project_id, 'project_name');
        $data['project_id'] = $project_id;
        $bank_transaction = $this->banktransactions_model->get_banktransaction_listALL($project_id, 1);
        $invoice_betail = $this->invoices_model->get_invoice_unlock($project_id, 1, 0);
        $invoice_Ubetail = $this->invoices_model->get_invoice_unlock($project_id, 0, 0);
        $transaction_unlock = $bank_transaction->num_rows() + $invoice_betail->num_rows();
        $data['transaction_unlock'] = $transaction_unlock;
        $data['invoice_unlock'] = $invoice_Ubetail->num_rows();

        $this->load->view('admin/content/popup_info_project', $data);
    }


    public function set_project_defualt()
    {
        $project_id = $this->input->get('project_id');
        $user_id =  $this->session->userdata('user_id');

        $name = "setting_project_defualt_" . $user_id;
        $value = $project_id;
        // if($this->session->has_userdata($name)){
        //     $this->session->set_userdata(array($name=>$value));
        // }else{
        //      $this->session->set_userdata($name, $value);
        // }
        $query = $this->db->get_where('bf_settings', array('name' => $name));
        if ($query->num_rows() == 0) {
            $this->db->insert('bf_settings', array('name' => $name, 'module' => 'core', 'value' => $value));
        } else {
            $this->db->update('bf_settings', array('module' => 'core', 'value' => $value), array('name' => $name));
        }
    }

    public function hide_project()
    {
        $project_id = $this->input->get('project_id');
        $user_id =  $this->session->userdata('user_id');

        $name = "setting_dashboard_project_" . $user_id;

        $query = $this->db->get_where('bf_settings', array('name' => $name));
        if ($query->num_rows() == 0) {
            $data = array($project_id);
            $this->db->insert('bf_settings', array('name' => $name, 'module' => 'core', 'value' => json_encode($data)));
        } else {
            $data = $query->result_array();
            $data_value = json_decode($data[0]['value']);
            array_push($data_value, $project_id);
            $this->db->update('bf_settings', array('module' => 'core', 'value' => json_encode($data_value)), array('name' => $name));
        }
    }
    //--------------------------------------------------------------------

    /**
     * If the Auth lib is loaded, it will set the current user, since users
     * will never be needed if the Auth library is not loaded. By not requiring
     * this to be executed and loaded for every command, we can speed up calls
     * that don't need users at all, or rely on a different type of auth, like
     * an API or cronjob.
     *
     * Copied from Base_Controller
     */
    protected function set_current_user()
    {
        if (class_exists('Auth')) {
            // Load our current logged in user for convenience
            if ($this->auth->is_logged_in()) {
                $this->current_user = clone $this->auth->user();

                $this->current_user->user_img = gravatar_link($this->current_user->email, 22, $this->current_user->email, "{$this->current_user->email} Profile");

                // if the user has a language setting then use it
                if (isset($this->current_user->language)) {
                    $this->config->set_item('language', $this->current_user->language);
                }
            } else {
                $this->current_user = null;
            }

            // Make the current user available in the views
            if (!class_exists('Template')) {
                $this->load->library('Template');
            }
            Template::set('current_user', $this->current_user);
        }
    }

    public function checknewdataimport()
    {
        $projects = $this->db->get_where('bf_project', array('deleted' => 0, 'use_bank' => 0));
        $list_avd = $this->db->get('bf_avd');
        $list_supplier = $this->db->get('bf_supplier');
        $list_bank_account = $this->db->get('bf_bankaccount');
        $data_financial = json_decode(file_get_contents("http://finans.k12.no/assets/main_data.json"));
        $new_rows_project = count($data_financial->projects) - $projects->num_rows();
        $new_rows_avd = count($data_financial->avds) - $list_avd->num_rows();
        $new_rows_supplier = count($data_financial->suppliers) - $list_supplier->num_rows();
        $new_rows_bankaccount = count($data_financial->bankaccounts) - $list_bank_account->num_rows();
        //echo $new_rows_avd."+".$new_rows_project."+".$new_rows_bankaccount."+".$new_rows_supplier;
        return $new_rows_avd + $new_rows_project + $new_rows_bankaccount + $new_rows_supplier;
    }

    public function call_data_invoice()
    {
        $project_id = $this->input->get('project_id');
        //check new data project , supplier , department , bankaccount 
        $new_data_import = $this->checknewdataimport();
        if ($new_data_import != 0) {
            $this->output->set_output(0);
            exit();
        }
        //end check 
        //$url = 'http://financial.demo:88';
        $url = 'http://finans.k12.no';
        $html_data = file_get_contents($url . '/home/get_data_invoice_and_transaction?project_id=' . $_GET['project_id'] . '&type=invoice');

        $data_decode = json_decode($html_data);

        $query_supplier = $this->db->get_where('bf_supplier', array('deleted' => 0));
        $query_avd = $this->db->get('bf_avd');

        $data_new["list_supplier"] = null;
        foreach ($query_supplier->result() as $item) :
            $data_new["list_supplier"][$item->supplierid]["supplier_name"] = $item->supplier_name;
        endforeach;
        $data_new["list_avd"] = null;
        foreach ($query_avd->result() as $item) :
            $data_new["list_supplier"][$item->avd_original_id]["avd_name"] = $item->avd_name;
        endforeach;
        $data_new["list_invoices"] = array();
        //echo "<pre>";print_r($data_decode);echo "</pre>";
        //Start Add new invoice
        foreach ($data_decode->list_invoices as $item) :
            $this->db->select('ispaid, lock_account_invoice');
            $this->db->join('bf_invoice_accounting', 'bf_invoice_accounting.invoice_id = bf_invoice_upload.upload_id', 'left');
            $query_check_row_invoice = $this->db->get_where('bf_invoice_upload', array('upload_invoice_id' => $item->upload_invoice_id, 'project_id' => $item->project_id, 'supplierid' => $item->supplierid));
            if ($query_check_row_invoice->num_rows() == 0) {
                array_push($data_new["list_invoices"], $item);
            } else {
                //if($item->upload_id == 4240){ echo $this->db->last_query().$query_check_row_invoice->num_rows();} 
                foreach ($query_check_row_invoice->result() as $item_old_invoice) :
                    if ($item_old_invoice->ispaid == 0 && $item->ispaid == 1 && $item_old_invoice->lock_account_invoice == 1) {
                        array_push($data_new["list_invoices"], $item); //if($item->upload_invoice_id == '2015020'){ echo "come";}                
                    }
                    if ($item_old_invoice->lock_account_invoice == 0) {
                        array_push($data_new["list_invoices"], $item);   //if($item->upload_invoice_id == '2015020'){ echo "go";}                                      
                    }
                endforeach;
            }
        endforeach;
        //ENd Add new invoice

        //$_SESSION["update_data_result"] = json_encode($data_new);
        $this->load->view('admin/content/popup_update_data', $data_new);
    }

    public function call_data_transaction()
    {
        $project_id = $this->input->get('project_id');
        //check new data project , supplier , department , bankaccount 
        $new_data_import = $this->checknewdataimport();
        if ($new_data_import != 0) {
            $this->output->set_output(0);
            exit();
        }
        //end check 
        //$url = 'http://financial.demo:88';
        $url = 'http://finans.k12.no';
        $html_data = file_get_contents($url . '/home/get_data_invoice_and_transaction?project_id=' . $_GET['project_id'] . '&type=transaction');
        $data_decode = json_decode($html_data);

        $query_supplier = $this->db->get_where('bf_supplier', array('deleted' => 0));
        $query_avd = $this->db->get('bf_avd');

        $data_new["list_supplier"] = null;
        foreach ($query_supplier->result() as $item) :
            $data_new["list_supplier"][$item->supplierid]["supplier_name"] = $item->supplier_name;
        endforeach;
        $data_new["list_avd"] = null;
        foreach ($query_avd->result() as $item) :
            $data_new["list_supplier"][$item->avd_original_id]["avd_name"] = $item->avd_name;
        endforeach;

        //Start Add new invoice
        $data_new["list_invoices"] = array();
        if (!empty($data_decode->list_invoices) && count($data_decode->list_invoices) > 0) {
            foreach ($data_decode->list_invoices as $item) :
                $this->db->select('ispaid, lock_account_invoice');
                $this->db->join('bf_invoice_accounting', 'bf_invoice_accounting.invoice_id = bf_invoice_upload.upload_id', 'left');
                $query_check_row_invoice = $this->db->get_where('bf_invoice_upload', array('upload_invoice_id' => $item->upload_invoice_id, 'project_id' => $item->project_id, 'supplierid' => $item->supplierid));
                if ($query_check_row_invoice->num_rows() == 0) {
                    array_push($data_new["list_invoices"], $item);
                } else {
                    //if($item->upload_id == 4240){ echo $this->db->last_query().$query_check_row_invoice->num_rows();} 
                    foreach ($query_check_row_invoice->result() as $item_old_invoice) :
                        if ($item_old_invoice->lock_account_invoice == 0) {
                            array_push($data_new["list_invoices"], $item);   //if($item->upload_invoice_id == '2015020'){ echo "go";}                                      
                        }
                    endforeach;
                }
            endforeach;
        }
        //ENd Add new invoice

        //Start Add new transaction
        $data_new['list_transactions'] = array();
        foreach ($data_decode->list_transaction as $item) :
            $this->db->where('transaction_id', $item->transaction_id);
            $this->db->where('deleted', 0);
            $query_check_row_transaction = $this->db->get('bf_bank_transection');
            if ($query_check_row_transaction->num_rows() == 0) {
                array_push($data_new["list_transactions"], $item);
            } else {
                foreach ($query_check_row_transaction->result() as $item_old_transaction) :
                    if ($item_old_transaction->lock_account == 0) {
                        array_push($data_new["list_transactions"], $item);
                    }
                endforeach;
            }
        endforeach;
        //End Add new transaction
        //echo "<pre>";print_r($data_new['list_transactions']);echo "</pre>";exit();
        //$_SESSION["update_data_result"] = json_encode($data_new);
        $this->load->view('admin/content/popup_update_data_transaction', $data_new);
    }

    public function update_data_invoice_and_transactions()
    {
        //$data_decode = json_decode($_SESSION["update_data_result"]);
        $data_decode = json_decode($_POST['data_update']);
        $data_invoice_decode = (!empty($_POST['data_update_invoice'])) ? json_decode($_POST['data_update_invoice']) : null;
        $type = $_POST['type'];

        //$url = 'http://financial.demo:88';
        $url = 'http://finans.k12.no';
        // echo "<pre>";print_r($data_decode);echo "</pre>";
        // exit();
        if ($type == "invoice") {
            foreach ($data_decode as $item) :
                $success_insert = false;
                $this->db->select('upload_id, ispaid, lock_account_invoice');
                        $this->db->join('bf_invoice_accounting', 'bf_invoice_accounting.invoice_id = bf_invoice_upload.upload_id', 'left');
                        $query_check_row_invoice = $this->db->get_where('bf_invoice_upload', array('codeinvoice' => $item->codeinvoice, 'project_id' => $item->project_id, 'supplierid' => $item->supplierid));
                        //echo $this->db->last_query();
                        $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;
                        if ($query_check_row_invoice->num_rows() == 0) {
                            $data_insert = array(
                                'upload_invoice_id' => $item->upload_invoice_id,
                                'project_id' => $item->project_id,
                                'avd_id' => $item->avd_id,
                                'supplierid' => $item->supplierid,
                                'ispaid' => $item->ispaid,
                                'amount' => $item->amount,
                                'contact_number' => $item->contact_number,
                                'paid_date' => $item->paid_date,
                                'is_motregnet' => intval($item->is_motregnet),
                                'm_amount' => $item->m_amount,
                                'is_checked' => $item->is_checked,
                                'codeinvoice' => $item->codeinvoice,
                                'comment' => $item->comment,
                                'upload_name' => $pathFile,
                                'upload_date' => $item->upload_date,
                                'upload_by' => $item->upload_by,
                                'update_date' => $item->update_date,
                                'update_by' => $item->update_by
                            );

                            //$pathFile=(strpos($item->upload_name, 'assets/') === false)?'/assets/uploads/pdf/'.$item->upload_name:'/'.$item->upload_name;
                            // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                            // if (!file_exists($newpath)) {
                            //     copy($url.$pathFile, $newpath);
                            // }
                            $id = $this->db->insert('bf_invoice_upload', $data_insert);
                            //add account debit and credit
                            // $query_avd = $this->db->get_where('bf_avd',array('avd_original_id'=>$item->avd_id));
                            // if($query_avd->num_rows() > 0){
                            //     foreach($query_avd->result() as $item_avd):
                            //         $data_account = array(
                            //             'invoice_id'=>$id,
                            //             'account_debit_id'=>$item_avd->account_id,
                            //             'account_credit_id'=>$item_avd->account_credit_id,
                            //         );

                            //         $this->db->insert('bf_invoice_accounting',$data_account);
                            //     endforeach;
                            // }
                        } else {
                            foreach ($query_check_row_invoice->result() as $item_old_invoice) :
                                if ($item_old_invoice->ispaid == 0 && $item->ispaid == 1) {
                                    $this->db->update('bf_invoice_upload', array('ispaid' => $item->ispaid), array('upload_id' => $item_old_invoice->upload_id));
                                }
                                $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;

                                if ($item_old_invoice->lock_account_invoice == 0) {
                                    $data_update = array(
                                        'codeinvoice' => $item->codeinvoice,
                                        'avd_id' => $item->avd_id,
                                        'supplierid' => $item->supplierid,
                                        'ispaid' => intval($item->ispaid),
                                        'amount' => $item->amount,
                                        'contact_number' => $item->contact_number,
                                        'paid_date' => $item->paid_date,
                                        'm_amount' => $item->m_amount,
                                        'is_checked' => $item->is_checked,
                                        'comment' => $item->comment,
                                        'upload_name' => $pathFile
                                    );

                                    //download file
                                    // $pathFile=(strpos($item->upload_name, 'assets/') === false)?'/assets/uploads/pdf/'.$item->upload_name:'/'.$item->upload_name;
                                    // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                                    // if (!file_exists($newpath)) {
                                    //     copy($url.$pathFile, $newpath);
                                    // }
                                    $this->db->update('bf_invoice_upload', $data_update, array('upload_id' => $item_old_invoice->upload_id));
                                }
                            endforeach;
                        }
                // try {
                //     $data_insert = array(
                //         $item->upload_invoice_id,
                //         $item->project_id,
                //         $item->avd_id,
                //         $item->supplierid,
                //         $item->ispaid,
                //         $item->amount,
                //         $item->contact_number,
                //         $item->paid_date,
                //         intval($item->is_motregnet),
                //         $item->m_amount,
                //         $item->is_checked,
                //         $item->codeinvoice,
                //         $item->comment,
                //         $item->upload_name,
                //         $item->upload_date,
                //         $item->upload_by,
                //         $item->update_date,
                //         $item->update_by
                //     );
                //     $query_list_data = $this->db->query("call bf_importupdate_invoices(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", $data_insert);
                //     clean_mysqli_connection($this->db->conn_id);
                //     $success_insert = true;
                // } catch (Exception $e) {
                //     //error
                // } finally {
                //     if (!$success_insert) {
                //         // $this->db->select('upload_id, ispaid, lock_account_invoice');
                //         // $this->db->join('bf_invoice_accounting', 'bf_invoice_accounting.invoice_id = bf_invoice_upload.upload_id', 'left');
                //         // $query_check_row_invoice = $this->db->get_where('bf_invoice_upload', array('codeinvoice' => $item->codeinvoice, 'project_id' => $item->project_id, 'supplierid' => $item->supplierid));
                //         // //echo $this->db->last_query();
                //         // $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;
                //         // if ($query_check_row_invoice->num_rows() == 0) {
                //         //     $data_insert = array(
                //         //         'upload_invoice_id' => $item->upload_invoice_id,
                //         //         'project_id' => $item->project_id,
                //         //         'avd_id' => $item->avd_id,
                //         //         'supplierid' => $item->supplierid,
                //         //         'ispaid' => $item->ispaid,
                //         //         'amount' => $item->amount,
                //         //         'contact_number' => $item->contact_number,
                //         //         'paid_date' => $item->paid_date,
                //         //         'is_motregnet' => intval($item->is_motregnet),
                //         //         'm_amount' => $item->m_amount,
                //         //         'is_checked' => $item->is_checked,
                //         //         'codeinvoice' => $item->codeinvoice,
                //         //         'comment' => $item->comment,
                //         //         'upload_name' => $pathFile,
                //         //         'upload_date' => $item->upload_date,
                //         //         'upload_by' => $item->upload_by,
                //         //         'update_date' => $item->update_date,
                //         //         'update_by' => $item->update_by
                //         //     );

                //         //     //$pathFile=(strpos($item->upload_name, 'assets/') === false)?'/assets/uploads/pdf/'.$item->upload_name:'/'.$item->upload_name;
                //         //     // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                //         //     // if (!file_exists($newpath)) {
                //         //     //     copy($url.$pathFile, $newpath);
                //         //     // }
                //         //     $id = $this->db->insert('bf_invoice_upload', $data_insert);
                //         //     //add account debit and credit
                //         //     // $query_avd = $this->db->get_where('bf_avd',array('avd_original_id'=>$item->avd_id));
                //         //     // if($query_avd->num_rows() > 0){
                //         //     //     foreach($query_avd->result() as $item_avd):
                //         //     //         $data_account = array(
                //         //     //             'invoice_id'=>$id,
                //         //     //             'account_debit_id'=>$item_avd->account_id,
                //         //     //             'account_credit_id'=>$item_avd->account_credit_id,
                //         //     //         );

                //         //     //         $this->db->insert('bf_invoice_accounting',$data_account);
                //         //     //     endforeach;
                //         //     // }
                //         // } else {
                //         //     foreach ($query_check_row_invoice->result() as $item_old_invoice) :
                //         //         if ($item_old_invoice->ispaid == 0 && $item->ispaid == 1) {
                //         //             $this->db->update('bf_invoice_upload', array('ispaid' => $item->ispaid), array('upload_id' => $item_old_invoice->upload_id));
                //         //         }
                //         //         $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;

                //         //         if ($item_old_invoice->lock_account_invoice == 0) {
                //         //             $data_update = array(
                //         //                 'codeinvoice' => $item->codeinvoice,
                //         //                 'avd_id' => $item->avd_id,
                //         //                 'supplierid' => $item->supplierid,
                //         //                 'ispaid' => intval($item->ispaid),
                //         //                 'amount' => $item->amount,
                //         //                 'contact_number' => $item->contact_number,
                //         //                 'paid_date' => $item->paid_date,
                //         //                 'm_amount' => $item->m_amount,
                //         //                 'is_checked' => $item->is_checked,
                //         //                 'comment' => $item->comment,
                //         //                 'upload_name' => $pathFile
                //         //             );

                //         //             //download file
                //         //             // $pathFile=(strpos($item->upload_name, 'assets/') === false)?'/assets/uploads/pdf/'.$item->upload_name:'/'.$item->upload_name;
                //         //             // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                //         //             // if (!file_exists($newpath)) {
                //         //             //     copy($url.$pathFile, $newpath);
                //         //             // }
                //         //             $this->db->update('bf_invoice_upload', $data_update, array('upload_id' => $item_old_invoice->upload_id));
                //         //         }
                //         //     endforeach;
                //         // }
                //     }
                // }


            endforeach;
        } else if ($type == "transaction") {
            //invoice is paid
            if (!empty($data_invoice_decode)) {
                foreach ($data_invoice_decode as $item) :
                    $success_insert = false;
                    try {
                        $data_insert = array(
                            $item->upload_invoice_id,
                            $item->project_id,
                            $item->avd_id,
                            $item->supplierid,
                            $item->ispaid,
                            $item->amount,
                            $item->contact_number,
                            $item->paid_date,
                            intval($item->is_motregnet),
                            $item->m_amount,
                            $item->is_checked,
                            $item->codeinvoice,
                            $item->comment,
                            $item->upload_name,
                            $item->upload_date,
                            $item->upload_by,
                            $item->update_date,
                            $item->update_by
                        );
                        $query_list_data = $this->db->query("call bf_importupdate_invoices(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", $data_insert);
                        clean_mysqli_connection($this->db->conn_id);
                        $success_insert = true;
                    } catch (Exception $e) {
                        //error
                    } finally {
                        if (!$success_insert) {
                            $this->db->join('bf_invoice_accounting', 'bf_invoice_accounting.invoice_id = bf_invoice_upload.upload_id', 'left');
                            $query_check_row_invoice = $this->db->get_where('bf_invoice_upload', array('upload_invoice_id' => $item->upload_invoice_id, 'project_id' => $item->project_id, 'supplierid' => $item->supplierid));
                            //echo $this->db->last_query();
                            $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;

                            if ($query_check_row_invoice->num_rows() == 0) {
                                $data_insert = array(
                                    'upload_invoice_id' => $item->upload_invoice_id,
                                    'project_id' => $item->project_id,
                                    'avd_id' => $item->avd_id,
                                    'supplierid' => $item->supplierid,
                                    'ispaid' => $item->ispaid,
                                    'amount' => $item->amount,
                                    'contact_number' => $item->contact_number,
                                    'paid_date' => $item->paid_date,
                                    'is_motregnet' => intval($item->is_motregnet),
                                    'm_amount' => $item->m_amount,
                                    'is_checked' => $item->is_checked,
                                    'codeinvoice' => $this->invoices_model->setCodeInvoice($item->project_id),
                                    'comment' => $item->comment,
                                    'upload_name' => $pathFile,
                                    'upload_date' => $item->upload_date,
                                    'upload_by' => $item->upload_by,
                                    'update_date' => $item->update_date,
                                    'update_by' => $item->update_by
                                );

                                // $pathFile=(strpos($item->upload_name, 'assets/') === false)?'/assets/uploads/pdf/'.$item->upload_name:'/'.$item->upload_name;
                                // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                                // if (!file_exists($newpath)) {
                                //     copy($url.$pathFile, $newpath);
                                // }
                                $this->db->insert('bf_invoice_upload', $data_insert);
                            } else {
                                foreach ($query_check_row_invoice->result() as $item_old_invoice) :
                                    if ($item_old_invoice->ispaid == 0 && $item->ispaid == 1) {
                                        $this->db->update('bf_invoice_upload', array('ispaid' => $item->ispaid), array('upload_id' => $item_old_invoice->upload_id));
                                    }
                                    $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;

                                    if ($item_old_invoice->lock_account_invoice == 0) {
                                        $data_update = array(
                                            'avd_id' => $item->avd_id,
                                            'supplierid' => $item->supplierid,
                                            'ispaid' => intval($item->ispaid),
                                            'amount' => $item->amount,
                                            'contact_number' => $item->contact_number,
                                            'paid_date' => $item->paid_date,
                                            'm_amount' => $item->m_amount,
                                            'is_checked' => $item->is_checked,
                                            'comment' => $item->comment,
                                            'upload_name' => $pathFile
                                        );

                                        //download file
                                        // $pathFile=(strpos($item->upload_name, 'assets/') === false)?'/assets/uploads/pdf/'.$item->upload_name:'/'.$item->upload_name;
                                        // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                                        // if (!file_exists($newpath)) {
                                        //     copy($url.$pathFile, $newpath);
                                        // }
                                        $this->db->update('bf_invoice_upload', $data_update, array('upload_id' => $item_old_invoice->upload_id));
                                    }
                                endforeach;
                            }
                        }
                    }

                endforeach;
            }
            //end invoice is paid
            foreach ($data_decode as $item) :
                // $query_type = $this->db->get_where('bf_transection_types', array('type_id' => $item->type));
                // $type_transaction = $query_type->result_array();
                // $debit_account_id = null;
                // $credit_account_id = null;
                // if (count($type_transaction) > 0) {
                //     if (($item->from_id == 41 || $item->to_id == 41) && $item->type == 3) {
                //         $debit = ($item->from_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                //         $credit = ($item->to_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                //         $debit_account_id = $debit;
                //         $credit_account_id = $credit;
                //     } else {
                //         $debit_account_id = $type_transaction[0]['debit_account_id'];
                //         $credit_account_id = $type_transaction[0]['credit_account_id'];
                //     }
                // }

                // $data_insert = array(
                //     $item->transaction_id,
                //     $item->from_id,
                //     $item->to_id,
                //     $item->type,
                //     $item->amount,
                //     $item->attachment,
                //     $item->transection_date,
                //     $item->comment,
                //     intval($item->is_show),
                //     intval($item->is_checked),
                //     date('Y-m-d H:i:s'),
                //     $debit_account_id,
                //     $credit_account_id
                // );
                // $query_list_data = $this->db->query("call bf_importupdate_transactions(?,?,?,?,?,?,?,?,?,?,?,?,?)", $data_insert);
                // clean_mysqli_connection($this->db->conn_id);
            //get type 
            $query_type = $this->db->get_where('bf_transection_types', array('type_id' => $item->type));
            $type_transaction = $query_type->result_array();
            //print_r($type_transaction);
            //get transaction
            $this->db->where('transaction_id', $item->transaction_id);
            $this->db->where('deleted', 0);
            $query_check_row_transaction = $this->db->get('bf_bank_transection');
            if ($query_check_row_transaction->num_rows() == 0) {
                $data = array(
                    'transaction_id' => $item->transaction_id,
                    'from_id' => $item->from_id,
                    'to_id' => $item->to_id,
                    'type' => $item->type,
                    'amount' => $item->amount,
                    'attachment' => $item->attachment,
                    'transection_date' => $item->transection_date,
                    'comment' => $item->comment,
                    'is_show' => $item->is_show,
                    'is_checked' => $item->is_checked,
                    'created_on' => date('Y-m-d H:i:s')
                );

                if (count($type_transaction) > 0) {
                    if (($item->from_id == 41 || $item->to_id == 41) && $item->type == 3) {
                        $debit = ($item->from_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                        $credit = ($item->to_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                        $data['debit_account_id'] = $debit;
                        $data['credit_account_id'] = $credit;
                    } else {
                        $data['debit_account_id'] = $type_transaction[0]['debit_account_id'];
                        $data['credit_account_id'] = $type_transaction[0]['credit_account_id'];
                    }
                }

                // $pathFile='/assets/uploads/transactions/'.$item->attachment;
                // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                // if (!file_exists($newpath)) {
                //     copy($url.$pathFile, $newpath);
                // }

                $this->db->insert('bf_bank_transection', $data);
            } else {

                $data = array(
                    'amount' => $item->amount,
                    'attachment' => $item->attachment,
                    'transection_date' => $item->transection_date,
                    'comment' => $item->comment,
                    'is_show' => $item->is_show,
                    'is_checked' => $item->is_checked,
                    'modified_on' => date('Y-m-d H:i:s')
                );

                foreach ($query_check_row_transaction->result() as $item_older) {
                    if (empty($item_older->debit_account_id) && empty($item_older->credit_account_id) && count($type_transaction) > 0) {
                        if (($item->from_id == 41 || $item->to_id == 41) && $item->type == 3) {
                            $debit = ($item->from_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                            $credit = ($item->to_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                            $data['debit_account_id'] = $debit;
                            $data['credit_account_id'] = $credit;
                        } else {
                            $data['debit_account_id'] = $type_transaction[0]['debit_account_id'];
                            $data['credit_account_id'] = $type_transaction[0]['credit_account_id'];
                        }
                    }
                }

                // $pathFile='/assets/uploads/transactions/'.$item->attachment;
                // $newpath = $_SERVER['DOCUMENT_ROOT'].$pathFile;//'/assets/uploads/pdf/'.$item->upload_name;
                // if (!file_exists($newpath)) {
                //     copy($url.$pathFile, $newpath);
                // }

                $this->db->update('bf_bank_transection', $data, array('transaction_id' => $item->transaction_id));
            }
            endforeach;
        }

        //ENd Add new invoice
        //            
        //            //Start Add new transaction
        ////            foreach($data_decode->list_transaction as $item):
        ////                
        ////            endforeach;
        //            //End Add new transaction
        $this->output->set_output(1);
    }

    public function import_data()
    {
        $import_name = $this->input->get('import_name');
        //$url = 'http://financial.demo:88';
        $url = 'http://finans.k12.no';

        //data projects
        if ($import_name == 'projects') {
            //$html_data = file_get_contents($url.'/financial_api/get_data_projects_import');
            $data_decode = json_decode(file_get_contents($this->data_financial)); //json_decode($html_data);
            //echo "<pre>"; print_r($data_decode); echo "</pre>";
            $data['new_data'] = $data_decode->projects;
            //get project in regnskap
            $query = $this->db->get('bf_project');
            $data['old_data'] = $query->result_array();
            $data['new_data_insert'] = array();
            foreach ($data['new_data'] as $item) :
                $query_check = $this->db->get_where('bf_project', array('project_original_id' => $item->project_original_id));
                if ($query_check->num_rows() == 0) {
                    array_push($data["new_data_insert"], $item);
                }
            endforeach;

            //echo "<pre>"; print_r($data['new_data_insert']); echo "</pre>";
            $this->load->view('admin/content/popup_import_projects', $data);
        }

        //data suppliers
        if ($import_name == 'suppliers') {
            $html_data = file_get_contents($url . '/home/get_data_suppliers_import');
            $data_decode = json_decode($html_data);
            $data['data_supplier'] = $data_decode->supplier;
            $data['data_supplier_project'] = $data_decode->supplier_project;
            //get project in regnskap
            $query = $this->db->get('bf_supplier');
            $data['old_data_supplier'] = $query->result_array();
            $data['new_data_supplier_insert'] = array();
            $data['new_data_supplier_update'] = array();
            foreach ($data['data_supplier'] as $item) :
                $query_check = $this->db->get_where('bf_supplier', array('supplierid' => $item->supplierid));
                if ($query_check->num_rows() == 0) {
                    array_push($data["new_data_supplier_insert"], $item);
                } else {
                    foreach ($query_check->result() as $item_supplier) :
                        if ($item_supplier->supplier_name != $item->supplier_name) :
                            array_push($data["new_data_supplier_update"], $item);
                        endif;
                    endforeach;
                }
            endforeach;


            $this->load->view('admin/content/popup_import_suppliers', $data);
        }

        //data avds
        if ($import_name == 'avds') {
            $html_data = file_get_contents($url . '/home/get_data_avd_import');
            $data_decode = json_decode($html_data);
            $data['data_avd'] = $data_decode->avd;
            $data['data_avd_project'] = $data_decode->avd_project;
            //get project in regnskap
            $query = $this->db->get('bf_avd');
            $data['old_data_avd'] = $query->result_array();
            $data['new_data_avd_insert'] = array();
            foreach ($data['data_avd'] as $item) :
                $query_check = $this->db->get_where('bf_avd', array('avd_original_id' => $item->avd_original_id));
                if ($query_check->num_rows() == 0) {
                    array_push($data["new_data_avd_insert"], $item);
                }
            endforeach;

            $this->load->view('admin/content/popup_import_avds', $data);
        }

        //bankaccount
        if ($import_name == 'bankaccount') {
            $html_data = file_get_contents($url . '/home/get_data_bankaccount_import');
            $data_decode = json_decode($html_data);
            $data['data_bankaccount'] = $data_decode->bankaccount;
            $data['data_type_transaction'] = $data_decode->type_transaction;
            //get project in regnskap
            $query = $this->db->get('bf_bankaccount');
            $data['old_data_bankaccount'] = $query->result_array();
            $data['new_data_bankaccount_insert'] = array();
            foreach ($data['data_bankaccount'] as $item) :
                $query_check = $this->db->get_where('bf_bankaccount', array('bankaccountid' => $item->bankaccountid));
                if ($query_check->num_rows() == 0) {
                    array_push($data["new_data_bankaccount_insert"], $item);
                }
            endforeach;

            $data['new_data_type_insert'] = array();
            foreach ($data['data_type_transaction'] as $item) :
                $query_check = $this->db->get_where('bf_transection_types', array('type_id' => $item->type_id));
                if ($query_check->num_rows() == 0) {
                    array_push($data["new_data_type_insert"], $item);
                }
            endforeach;

            $this->load->view('admin/content/popup_import_bankaccounts', $data);
        }
    }

    public function save_data_projects()
    {
        $data_decode = json_decode($_POST['data_update']);
        $data_old_data = json_decode($_POST['data_old_data']);
        // foreach($data_decode as $item):
        //     $query = $this->db->get_where('bf_project',array('project_original_id'=>$item->project_original_id));
        //     if($query->num_rows() == 0){
        //         $this->db->insert('bf_project',$item);
        //     }else{
        //         $this->db->update('bf_project',$item,array('project_original_id'=>$item->project_original_id));
        //     }
        // endforeach;
        //$url = 'http://financial.demo:88';
        $url = 'http://finans.k12.no';
        // Set line to collect lines that wrap
        // echo "<pre>"; print_r($data_decode); echo "</pre>";
        if (count($data_decode) > 0) {
            if (count($data_old_data) == 0) {
                $folder_name = $this->server_path_sql . 'bf_project.sql';
                $file_restore = $this->load->file($folder_name, true);
                $file_array = explode(';', $file_restore);
                $num = count($file_array);
                $i = 1;
                foreach ($file_array as $query) {
                    if ($i < $num) {
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                        $this->db->query($query);
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                    }
                    $i++;
                }
            } else {
                foreach ($data_decode as $item) :
                    $query = $this->db->get_where('bf_project', array('project_original_id' => $item->project_original_id));
                    if ($query->num_rows() == 0) {
                        $this->db->insert('bf_project', $item);
                    } else {
                        $this->db->update('bf_project', $item, array('project_original_id' => $item->project_original_id));
                    }
                endforeach;
            }
        }

        $this->output->set_output(1);
    }

    public function save_data_suppliers()
    {
        $data_decode = json_decode($_POST['data_update_supplier']);
        $old_data_decode = json_decode($_POST['old_data_supplier']);
        $data_update_supplier_change = json_decode($_POST['data_update_supplier_change']);

        // foreach($data_decode as $item):
        //     $query_check = $this->db->get_where('bf_supplier',array('supplierid'=>$item->supplierid));
        //     if($query_check->num_rows() == 0){
        //         $this->db->insert('bf_supplier',$item);
        //     }else{
        //         $this->db->update('bf_supplier',$item,array('supplierid'=>$item->supplierid));
        //     }
        // endforeach;

        // $this->db->empty_table('bf_supplier_in_project');
        // foreach($data_decode_supplier_project as $item):
        //     $this->db->insert('bf_supplier_in_project',$item);
        // endforeach;
        if (count($data_decode) > 0) {

            if (count($old_data_decode) == 0) {
                $folder_name = $this->server_path_sql . 'bf_supplier.sql';
                $file_restore = $this->load->file($folder_name, true);
                $file_array = explode(';', $file_restore);
                $num = count($file_array);
                $i = 1;
                foreach ($file_array as $query) {
                    if ($i < $num) {
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                        $this->db->query($query);
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                    }
                    $i++;
                }
            } else {
                foreach ($data_decode as $item) :
                    $query_check = $this->db->get_where('bf_supplier', array('supplierid' => $item->supplierid));
                    if ($query_check->num_rows() == 0) {
                        $this->db->insert('bf_supplier', $item);
                    } else {
                        //$this->db->update('bf_supplier',$item,array('supplierid'=>$item->supplierid));
                    }
                endforeach;
            }
        }
        if (count($data_update_supplier_change) > 0) {
            foreach ($data_update_supplier_change as $item) :
                $this->db->update('bf_supplier', array('supplier_name' => $item->supplier_name), array('supplierid' => $item->supplierid));
            endforeach;
        }
        $this->output->set_output(1);
    }

    public function save_data_avds()
    {
        $data_decode = json_decode($_POST['data_update_avd']);
        $data_old_avd = json_decode($_POST['data_old_avd']);
        // $data_decode_avd_project = json_decode($_POST['data_update_avd_project']);

        // foreach($data_decode as $item):
        //     $query_check = $this->db->get_where('bf_avd',array('avd_original_id'=>$item->avd_original_id));
        //     if($query_check->num_rows() == 0){
        //         $this->db->insert('bf_avd',$item);
        //     }else{
        //         $this->db->update('bf_avd',$item,array('avd_original_id'=>$item->avd_original_id));
        //     }
        // endforeach;

        // $this->db->empty_table('bf_avd_projects');
        // foreach($data_decode_avd_project as $item):
        //     $this->db->insert('bf_avd_projects',$item);
        // endforeach;
        if (count($data_decode) > 0) {
            if (count($data_old_avd) == 0) {
                $folder_name = $this->server_path_sql . 'bf_avd.sql';
                $file_restore = $this->load->file($folder_name, true);
                $file_array = explode(';', $file_restore);
                $num = count($file_array);
                $i = 1;
                foreach ($file_array as $query) {
                    if ($i < $num) {
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                        $this->db->query($query);
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                    }
                    $i++;
                }

                $folder_name = $this->server_path_sql . 'bf_avd_projects.sql';
                $file_restore = $this->load->file($folder_name, true);
                $file_array = explode(';', $file_restore);
                $num = count($file_array);
                $i = 1;
                foreach ($file_array as $query) {
                    if ($i < $num) {
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                        $this->db->query($query);
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                    }
                    $i++;
                }
            } else {
                foreach ($data_decode as $item) :
                    $query_check = $this->db->get_where('bf_avd', array('avd_original_id' => $item->avd_original_id));
                    if ($query_check->num_rows() == 0) {
                        $this->db->insert('bf_avd', $item);
                    } else {
                        $this->db->update('bf_avd', $item, array('avd_original_id' => $item->avd_original_id));
                    }
                endforeach;
            }
        }

        $this->output->set_output(1);
    }


    public function save_data_bankaccounts()
    {
        $data_decode = json_decode($_POST['data_update']);
        $data_decode_type = json_decode($_POST['data_update_type']);
        $old_data_update = json_decode($_POST['old_data_update']);

        if (count($data_decode) > 0) {
            // foreach($data_decode as $item):
            //     $query = $this->db->get_where('bf_bankaccount',array('bankaccountid'=>$item->bankaccountid));
            //     if($query->num_rows() == 0){
            //         $this->db->insert('bf_bankaccount',$item);
            //     }else{
            //         $this->db->update('bf_bankaccount',$item,array('bankaccountid'=>$item->bankaccountid));
            //     }
            // endforeach;
            $folder_name = $this->server_path_sql . 'bf_bankaccount.sql';
            //echo $folder_name;
            $file_restore = $this->load->file($folder_name, true);
            $file_array = explode(';', $file_restore);
            $num = count($file_array);
            $i = 1;
            foreach ($file_array as $query) {
                if ($i < $num) {
                    $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                    $this->db->query($query);
                    $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                }
                $i++;
            }
        }

        $query_type_for_invoice = $this->db->get_where('bf_transection_types', array('invoice_type' => 1));
        $data_type_invoice = null;

        if ($query_type_for_invoice->num_rows() > 0) {
            $type_invoice = $query_type_for_invoice->result_array();
            $data_type_invoice = array(
                'name' => $type_invoice[0]['name'],
                'debit_account_id' => $type_invoice[0]['debit_account_id'],
                'credit_account_id' => $type_invoice[0]['credit_account_id'],
                'invoice_type' => $type_invoice[0]['invoice_type']
            );
        }

        if (count($data_decode_type) > 0) {
            foreach ($data_decode_type as $item) :

                $query = $this->db->get_where('bf_transection_types', array('type_id' => $item->type_id));
                if ($query->num_rows() == 0) {
                    $this->db->insert('bf_transection_types', $item);
                } else {
                    $this->db->update('bf_transection_types', $item, array('type_id' => $item->type_id));
                }
            endforeach;
            $check_type_for_invoice = $this->db->get_where('bf_transection_types', array('invoice_type' => 1));
            if ($check_type_for_invoice->num_rows() == 0 && $data_type_invoice != null) {
                $this->db->insert('bf_transection_types', $data_type_invoice);
            }
        }
        $this->output->set_output(1);
    }


    //EXPORT DB
    public function export_regnskap()
    {
        $prefs = array(
            'format'      => 'sql',
            'filename'    => 'regnskap_db_backup.sql'
        );


        $backup = $this->dbutil->backup($prefs);

        $db_name = 'backup-on-' . date("Y-m-d") . '.sql';
        $save = 'assets/' . $db_name;

        $this->load->helper('file');
        write_file($save, $backup);
    }


    public function adminupdatepath()
    {
        $type = $this->input->get('type');
        $url = 'http://finans.k12.no';
        if ($type == 'invoice') {
            $query = $this->db->get('bf_invoice_upload');
            foreach ($query->result() as $item) :
                if (stripos($item->upload_name, $this->s3_url) === false) {

                    $path = $item->upload_name;
                    $new_path = "";
                    if (stripos($item->upload_name, $url) === true) {
                        $new_path = str_replace($url . '/assets/uploads', $this->s3_url . "/" . $this->bucket_name . "/" . $this->folder_name, $path);
                    } else {
                        $new_path = $this->s3_url . "/" . $this->bucket_name . "/" . $this->folder_name . '/pdf' . $path;
                    }

                    $this->db->update('bf_invoice_upload', array('upload_name' => $new_path), array('upload_id' => $item->upload_id));
                } else {
                    //echo $item->upload_name."<br>";
                    if (stripos($item->upload_name, $url) > 0) {

                        $new_path = $item->upload_name;
                        if (stripos($item->upload_name, $url . '/assets/uploads/pdf') > 0) {
                            $new_path = str_replace($url . '/assets/uploads/pdf', '', $item->upload_name);
                        } else if (stripos($item->upload_name, $url . '/assets/uploads/attachments') > 0) {
                            $new_path = str_replace('pdf' . $url . '/assets/uploads/attachments', 'attachments', $item->upload_name);
                        }
                        //echo $new_path."<br>";
                        $this->db->update('bf_invoice_upload', array('upload_name' => $new_path), array('upload_id' => $item->upload_id));
                    } else {
                        if (stripos($item->upload_name, "/pdf") > 0 && stripos($item->upload_name, "pdf/")  <= 0 && stripos($item->upload_name, "/pdfassets") <= 0) {
                            $new_path = str_replace("/pdf", "/pdf/", $item->upload_name);
                            //echo $new_path."<br>";
                            $this->db->update('bf_invoice_upload', array('upload_name' => $new_path), array('upload_id' => $item->upload_id));
                        } else if (stripos($item->upload_name, 'pdfassets/uploads/attachments') > 0) {
                            $new_path = str_replace("pdfassets/uploads/attachments", "attachments", $item->upload_name);
                            //echo $new_path."<br>";
                            $this->db->update('bf_invoice_upload', array('upload_name' => $new_path), array('upload_id' => $item->upload_id));
                        }
                    }
                }
            endforeach;
        } else if ($type == 'transaction') {
            $query = $this->db->get('bf_bank_transection');
            foreach ($query->result() as $item) :
                if (stripos($item->attachment, $this->s3_url) === false && !empty($item->attachment)) {
                    $path = $this->s3_url . "/" . $this->bucket_name . "/" . $this->folder_name . "/" . "transactions/" . $item->attachment;

                    $this->db->update('bf_bank_transection', array('attachment' => $path), array('transaction_id' => $item->transaction_id));
                } else {
                    if (!empty($item->attachment)) {
                        $path = str_replace('//transactions', "/transactions", $item->attachment);
                        $this->db->update('bf_bank_transection', array('attachment' => $path), array('transaction_id' => $item->transaction_id));
                    }
                }
            endforeach;
        } else if ($type == 'contract') {
            $query = $this->db->get('bf_contact');
            foreach ($query->result() as $item) :
                if (stripos($item->filename, $this->s3_url) === false && !empty($item->filename)) {
                    $path = $this->s3_url . "/" . $this->bucket_name . "/" . $this->folder_name . "/" . "contract/" . $item->filename;

                    $this->db->update('bf_contact', array('filename' => $path), array('contactid' => $item->contactid));
                } else {
                    $path = str_replace('//contract', "/contract", $item->filename);
                    $this->db->update('bf_contact', array('filename' => $path), array('contactid' => $item->contactid));
                }
            endforeach;

            $query = $this->db->get('bf_contact_documents');
            foreach ($query->result() as $item) :
                if (stripos($item->filepath, $this->s3_url) === false && !empty($item->filepath)) {
                    $path = $this->s3_url . "/" . $this->bucket_name . "/" . $this->folder_name . "/" . "contract/" . $item->filepath;

                    $this->db->update('bf_contact_documents', array('filepath' => $path), array('document_id' => $item->document_id));
                } else {
                    $path = str_replace('//contract', "/contract", $item->filepath);
                    $this->db->update('bf_contact_documents', array('filepath' => $path), array('document_id' => $item->document_id));
                }
            endforeach;
        }

        exit();
    }

    /** Robot update invoice and transaction */
    public function robot_update_invoices()
    {
        $projects = $this->projects_model->get_list_project_special("", "", "", "all", false, null, 1);
        foreach ($projects->result() as $project) :

            $url = 'http://finans.k12.no';
            $html_data = file_get_contents($url . '/home/get_data_invoice_and_transaction?project_id=' . $project->project_original_id . '&type=invoice');
            $data_decode = json_decode($html_data);

            $data_new = null;
            foreach ($data_decode->list_invoices as $item) :
                $data_insert = array(
                    $item->upload_invoice_id,
                    $item->project_id,
                    $item->avd_id,
                    $item->supplierid,
                    $item->ispaid,
                    $item->amount,
                    $item->contact_number,
                    $item->paid_date,
                    intval($item->is_motregnet),
                    $item->m_amount,
                    $item->is_checked,
                    $item->codeinvoice,
                    $item->comment,
                    $item->upload_name,
                    $item->upload_date,
                    $item->upload_by,
                    $item->update_date,
                    $item->update_by
                );
                $query_list_data = $this->db->query("call bf_importupdate_invoices(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", $data_insert);
                clean_mysqli_connection($this->db->conn_id);

            endforeach;

        endforeach;

        exit();
    }

    public function update_data_invoice_and_transactions2()
    {
        //$data_decode = json_decode($_SESSION["update_data_result"]);
        $data_decode = json_decode($_POST['data_update']);
        $data_invoice_decode = (!empty($_POST['data_update_invoice'])) ? json_decode($_POST['data_update_invoice']) : null;
        $type = $_POST['type'];

        //$url = 'http://financial.demo:88';
        $url = 'http://finans.k12.no';
        //print_r($data_decode);
        if ($type == "invoice") {
            foreach ($data_decode as $item) :
                $this->db->select('upload_id, ispaid, lock_account_invoice');
                $this->db->join('bf_invoice_accounting', 'bf_invoice_accounting.invoice_id = bf_invoice_upload.upload_id', 'left');
                $query_check_row_invoice = $this->db->get_where('bf_invoice_upload', array('codeinvoice' => $item->codeinvoice, 'project_id' => $item->project_id, 'supplierid' => $item->supplierid));
                //echo $this->db->last_query();
                $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;
                if ($query_check_row_invoice->num_rows() == 0) {
                    $data_insert = array(
                        'upload_invoice_id' => $item->upload_invoice_id,
                        'project_id' => $item->project_id,
                        'avd_id' => $item->avd_id,
                        'supplierid' => $item->supplierid,
                        'ispaid' => $item->ispaid,
                        'amount' => $item->amount,
                        'contact_number' => $item->contact_number,
                        'paid_date' => $item->paid_date,
                        'is_motregnet' => intval($item->is_motregnet),
                        'm_amount' => $item->m_amount,
                        'is_checked' => $item->is_checked,
                        'codeinvoice' => $item->codeinvoice,
                        'comment' => $item->comment,
                        'upload_name' => $pathFile,
                        'upload_date' => $item->upload_date,
                        'upload_by' => $item->upload_by,
                        'update_date' => $item->update_date,
                        'update_by' => $item->update_by
                    );

                    $id = $this->db->insert('bf_invoice_upload', $data_insert);
                } else {
                    foreach ($query_check_row_invoice->result() as $item_old_invoice) :
                        if ($item_old_invoice->ispaid == 0 && $item->ispaid == 1) {
                            $this->db->update('bf_invoice_upload', array('ispaid' => $item->ispaid), array('upload_id' => $item_old_invoice->upload_id));
                        }
                        $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;

                        if ($item_old_invoice->lock_account_invoice == 0) {
                            $data_update = array(
                                'codeinvoice' => $item->codeinvoice,
                                'avd_id' => $item->avd_id,
                                'supplierid' => $item->supplierid,
                                'ispaid' => intval($item->ispaid),
                                'amount' => $item->amount,
                                'contact_number' => $item->contact_number,
                                'paid_date' => $item->paid_date,
                                'm_amount' => $item->m_amount,
                                'is_checked' => $item->is_checked,
                                'comment' => $item->comment,
                                'upload_name' => $pathFile
                            );
                            $this->db->update('bf_invoice_upload', $data_update, array('upload_id' => $item_old_invoice->upload_id));
                        }
                    endforeach;
                }
            endforeach;
        } else if ($type == "transaction") {
            //invoice is paid
            if (!empty($data_invoice_decode)) {
                foreach ($data_invoice_decode as $item) :
                    $this->db->join('bf_invoice_accounting', 'bf_invoice_accounting.invoice_id = bf_invoice_upload.upload_id', 'left');
                    $query_check_row_invoice = $this->db->get_where('bf_invoice_upload', array('upload_invoice_id' => $item->upload_invoice_id, 'project_id' => $item->project_id, 'supplierid' => $item->supplierid));
                    //echo $this->db->last_query();
                    $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;

                    if ($query_check_row_invoice->num_rows() == 0) {
                        $data_insert = array(
                            'upload_invoice_id' => $item->upload_invoice_id,
                            'project_id' => $item->project_id,
                            'avd_id' => $item->avd_id,
                            'supplierid' => $item->supplierid,
                            'ispaid' => $item->ispaid,
                            'amount' => $item->amount,
                            'contact_number' => $item->contact_number,
                            'paid_date' => $item->paid_date,
                            'is_motregnet' => intval($item->is_motregnet),
                            'm_amount' => $item->m_amount,
                            'is_checked' => $item->is_checked,
                            'codeinvoice' => $this->invoices_model->setCodeInvoice($item->project_id),
                            'comment' => $item->comment,
                            'upload_name' => $pathFile,
                            'upload_date' => $item->upload_date,
                            'upload_by' => $item->upload_by,
                            'update_date' => $item->update_date,
                            'update_by' => $item->update_by
                        );

                        $this->db->insert('bf_invoice_upload', $data_insert);
                    } else {
                        foreach ($query_check_row_invoice->result() as $item_old_invoice) :
                            if ($item_old_invoice->ispaid == 0 && $item->ispaid == 1) {
                                $this->db->update('bf_invoice_upload', array('ispaid' => $item->ispaid), array('upload_id' => $item_old_invoice->upload_id));
                            }
                            $pathFile = $item->upload_name; //(strpos($item->upload_name, 'assets/') === false)?$url.'/assets/uploads/pdf/'.$item->upload_name:$url.'/'.$item->upload_name;

                            if ($item_old_invoice->lock_account_invoice == 0) {
                                $data_update = array(
                                    'avd_id' => $item->avd_id,
                                    'supplierid' => $item->supplierid,
                                    'ispaid' => intval($item->ispaid),
                                    'amount' => $item->amount,
                                    'contact_number' => $item->contact_number,
                                    'paid_date' => $item->paid_date,
                                    'm_amount' => $item->m_amount,
                                    'is_checked' => $item->is_checked,
                                    'comment' => $item->comment,
                                    'upload_name' => $pathFile
                                );

                                $this->db->update('bf_invoice_upload', $data_update, array('upload_id' => $item_old_invoice->upload_id));
                            }
                        endforeach;
                    }
                endforeach;
            }
            //end invoice is paid
            foreach ($data_decode as $item) :
                //get type 
                $query_type = $this->db->get_where('bf_transection_types', array('type_id' => $item->type));
                $type_transaction = $query_type->result_array();
                //print_r($type_transaction);
                //get transaction
                $this->db->where('transaction_id', $item->transaction_id);
                $this->db->where('deleted', 0);
                $query_check_row_transaction = $this->db->get('bf_bank_transection');
                if ($query_check_row_transaction->num_rows() == 0) {
                    $data = array(
                        'transaction_id' => $item->transaction_id,
                        'from_id' => $item->from_id,
                        'to_id' => $item->to_id,
                        'type' => $item->type,
                        'amount' => $item->amount,
                        'attachment' => $item->attachment,
                        'transection_date' => $item->transection_date,
                        'comment' => $item->comment,
                        'is_show' => $item->is_show,
                        'is_checked' => $item->is_checked,
                        'created_on' => date('Y-m-d H:i:s')
                    );

                    if (count($type_transaction) > 0) {
                        if (($item->from_id == 41 || $item->to_id == 41) && $item->type == 3) {
                            $debit = ($item->from_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                            $credit = ($item->to_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                            $data['debit_account_id'] = $debit;
                            $data['credit_account_id'] = $credit;
                        } else {
                            $data['debit_account_id'] = $type_transaction[0]['debit_account_id'];
                            $data['credit_account_id'] = $type_transaction[0]['credit_account_id'];
                        }
                    }

                    $this->db->insert('bf_bank_transection', $data);
                } else {

                    $data = array(
                        'amount' => $item->amount,
                        'attachment' => $item->attachment,
                        'transection_date' => $item->transection_date,
                        'comment' => $item->comment,
                        'is_show' => $item->is_show,
                        'is_checked' => $item->is_checked,
                        'modified_on' => date('Y-m-d H:i:s')
                    );

                    foreach ($query_check_row_transaction->result() as $item_older) {
                        if (empty($item_older->debit_account_id) && empty($item_older->credit_account_id) && count($type_transaction) > 0) {
                            if (($item->from_id == 41 || $item->to_id == 41) && $item->type == 3) {
                                $debit = ($item->from_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                                $credit = ($item->to_id == 41) ? $type_transaction[0]['debit_account_id'] : $type_transaction[0]['credit_account_id'];
                                $data['debit_account_id'] = $debit;
                                $data['credit_account_id'] = $credit;
                            } else {
                                $data['debit_account_id'] = $type_transaction[0]['debit_account_id'];
                                $data['credit_account_id'] = $type_transaction[0]['credit_account_id'];
                            }
                        }
                    }
                    $this->db->update('bf_bank_transection', $data, array('transaction_id' => $item->transaction_id));
                }
            endforeach;
        }
    }
}
/* end ./application/controllers/home.php */
