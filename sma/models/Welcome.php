<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }
        $this->load->library('form_validation');
        $this->load->model('db_model');
        $this->load->model('cluster_model');
        $this->load->model('companies_model');
        $this->load->model('purchases_model');
        $this->load->model('products_model');
        $this->load->model('budget_model');
        $this->load->model('sales_model');
        $this->load->model('settings_model');
         $this->load->model('site');
    }

    public function index()
    {
        if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            redirect('sync');
        }
        
          
        $bc = array(array('link' => '#', 'page' => lang('Power BI dashboard')));
        $meta = array('page_title' => lang('Power BI dashboard'), 'bc' => $bc);
        $this->page_construct('index', $meta, $this->data);

    }

    function promotions()
    {
        $this->load->view($this->theme . 'promotions', $this->data);
    }

    function image_upload()
    {
        if (DEMO) {
            $error = array('error' => $this->lang->line('disabled_in_demo'));
            echo json_encode($error);
            exit;
        }
        $this->security->csrf_verify();
        if (isset($_FILES['file'])) {
            $this->load->library('upload');
            $config['upload_path'] = 'assets/uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '500';
            $config['max_width'] = $this->Settings->iwidth;
            $config['max_height'] = $this->Settings->iheight;
            $config['encrypt_name'] = TRUE;
            $config['overwrite'] = FALSE;
            $config['max_filename'] = 25;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $error = $this->upload->display_errors();
                $error = array('error' => $error);
                echo json_encode($error);
                exit;
            }
            $photo = $this->upload->file_name;
            $array = array(
                'filelink' => base_url() . 'assets/uploads/images/' . $photo
            );
            echo stripslashes(json_encode($array));
            exit;

        } else {
            $error = array('error' => 'No file selected to upload!');
            echo json_encode($error);
            exit;
        }
    }

    function set_data($ud, $value)
    {
        $this->session->set_userdata($ud, $value);
        echo true;
    }

    function hideNotification($id = NULL)
    {
        $this->session->set_userdata('hidden' . $id, 1);
        echo true;
    }

    function language($lang = false)
    {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }
        //$this->load->helper('cookie');
        $folder = 'sma/language/';
        $languagefiles = scandir($folder);
        if (in_array($lang, $languagefiles)) {
            $cookie = array(
                'name' => 'language',
                'value' => $lang,
                'expire' => '31536000',
                'prefix' => 'sma_',
                'secure' => false
            );

            $this->input->set_cookie($cookie);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    function download($file)
    {
        $this->load->helper('download');
        force_download('./files/'.$file, NULL);
        exit();
    }
    
    
    function search(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"datefrom"=>$post['datefrom'],"dateto"=>$post['dateto'],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"]);
    
    $filters="";
   foreach ($data as $key=>$value){
       if(is_array($value)){
           if(strtolower($key)=="countrys"){
             foreach ($value as $valuename) {
                $currency=$this->settings_model->getCurrencyByID($valuename);  
                $valuee.=$currency->country.".";
                
               }
              // die($valuee);
               
           }
           else  if(strtolower($key)=="cluster"){
               
               foreach ($value as $valuename) {
                $cluster=$this->settings_model->getClusterByID($valuename);  
                $valuee.=$cluster->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="customer"){
                foreach ($value as $valuename) {
                $company=$this->companies_model->getCompanyByID($valuename);  
                $valuee.=$company->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="productcategoryfamily"){
               foreach ($value as $valuename) {
                $category=$this->products_model->getCategoryById($valuename);  
                $valuee.=$category->name.".";
               }
               
               
           }
           else{
           $valuee.=implode(",",$value);
           }
       }
       else{$valuee=$value;}
       $filters.="&nbsp;<b>".ucwords($key).":</b>".strtoupper($valuee);
   }

       $groupedtotals=$this->sales_model->getGroupedSalesTotals($data);
       $groupedqty=$this->sales_model->getGroupedSalesTotalsQty($data);
    
     
      $customer=$this->input->post("customer");
     if(is_array($customer)){
         $customer=$customer[0];
     }
     else{
         $customer="all";
     }
     if(!$post['datefrom']){
         $data["datefrom"]="01/".date("Y");
     } 
     if(!$post['dateto']){
                  $data["dateto"]="12/".date("Y");
     }
         //post['datefrom'],$post['dateto'],$customer
     
      $stockcover=  $this->purchases_model->getStockCover($data,"sso"); 
     
    //  die(print_r($stockcover));
      if(is_array(json_decode($stockcover))){
         $this->data['stockcover']=$stockcover;
      }
      else{
          $this->data['stockcover']='[{"date":"01-2019","value":0},{"date":"02-2019","value":0}]';
      }
      
      
     $stockcoverpso=  $this->purchases_model->getStockCover($data,"pso"); 
     
     $stockcoversi=  $this->purchases_model->getStockCover($data,"si"); 
     
    //  die(print_r($stockcover));
      if(is_array(json_decode($stockcoverpso))){
         $this->data['stockcoverpso']=$stockcoverpso;
      }
      else{
          $this->data['stockcoverpso']='[{"date":"01-2019","value":0},{"date":"02-2019","value":0}]';
      }
if(is_array(json_decode($stockcoverpso))){
         $this->data['stockcoversi']=$stockcoversi;
      }
      else{
          $this->data['stockcoversi']='[{"date":"01-2019","value":0},{"date":"02-2019","value":0}]';
      }
        if(!is_array(json_decode($groupedtotals))){
            
      $this->data['groupedtotals'] ='[{"valuepso":"0","colorpso":"0"}]';
        }else{
            $this->data['groupedtotals'] = $groupedtotals;
        }
        
        if(!is_array(json_decode($groupedqty))){
             $this->data['groupedqty'] ='[{"valuepso":"0","colorpso":"0"}]';
    
        }else{
             $this->data['groupedqty']=$groupedqty;
        }
        
     
    
      
      
    
    
    
      $this->data['consolidated']=$this->sales_model->consolidatedSalesPso($data);
      /*[{"period":"2019-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-08","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-07","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-06","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-05","Actual":"88953.7200","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-04","Actual":"1851376.7300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-03","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-02","Actual":"105402.6300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-01","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-12","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-11","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-10","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0}]*/
       $this->data['consolidatedsso']=$this->sales_model->consolidatedSalesSSO($data);
        
      
      $consolidated=$this->sales_model->consolidatedSalesFamily($data);
      $this->data['consolidatedfamily']=$consolidated;
      //die(print_r( $this->data['consolidated']));
          
      $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales'] = $this->db_model->getLatestSales();
        
      //  $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        $this->data['chatData'] = $this->db_model->getChartData();
        $this->data['stock'] = $this->db_model->getStockValue();
        $this->data['bs'] = "";//$this->db_model->getBestSeller();
        $this->data['filters']=$filters;
        $lmsdate = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs'] = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc = array(array('link' => '#', 'page' => lang('dashboard')));
        $meta = array('page_title' => lang('Inventory & SISO Analysis'), 'bc' => $bc);
        $this->page_construct('dashboard', $meta, $this->data);
        
    }
    
    function si(){
       $post=$this->input->post();
       $this->load->model('sales_model');
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"datefrom"=>$post['datefrom'],"dateto"=>$post['dateto'],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"period"=>$post["period"]);
    $filters="";
    if($post){
            if(empty($post["period"])){
         $this->session->set_flashdata('error', $this->lang->line("please_select_period_first"));
            redirect($_SERVER["HTTP_REFERER"]);
    }
   foreach ($data as $key=>$value){
       if(is_array($value)){
           if(strtolower($key)=="countrys"){
             foreach ($value as $valuename) {
                $currency=$this->settings_model->getCurrencyByID($valuename);  
                $valuee.=$currency->country.".";
                
               }
              // die($valuee);
               
           }
           else  if(strtolower($key)=="cluster"){
               
             foreach ($value as $valuename) {
                $cluster=$this->settings_model->getClusterByID($valuename);  
                $valuee.=$cluster->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="customer"){
                foreach ($value as $valuename) {
                $company=$this->companies_model->getCompanyByID($valuename);  
                $valuee.=$company->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="productcategoryfamily"){
               foreach ($value as $valuename) {
                $category=$this->products_model->getCategoryById($valuename);  
                $valuee.=$category->name.".";
               }
               
               
           }
           else{
           $valuee.=implode(",",$value);
           }
       }
       else{$valuee=$value;}
       $filters.="&nbsp;<b>".ucwords($key).":</b>".strtoupper($valuee);
   }
    $salestotal=$this->sales_model->getSalesTotals($data);
    //die(print_r($salestotal));
    //get this year
    $period=end($post["period"]);
    $selectedyear= \substr($period,-4);
    $lastyearsalestotal=  $this->sales_model->getLastYearSalesTotal($data,$selectedyear-1);
    $thisyearsalestotal=$this->sales_model->getThisyearSalesTotal($data,$selectedyear);
      
    $consolidatedsi=  $this->sales_model->consolidatedSalesSumSI($data);
     $ytdsi=$this->sales_model->ytdSales($data,"SI",date("Y"));
     //{"period":"2019","Actual":441,"Budget":0,"Forecast":0,"Forecast2":0,"ActualLast":771}
   if(!is_object(json_decode($ytdsi))){
     $this->data["ytdaverage"]='{"period":"'.date('Y').'","Actual":0,"Budget":0,"Forecast":0,"Forecast2":0,"ActualLast":0}';
     $this->data["ytdaveragevariance"]='{"period":"'.date('Y').'","vsN1":0,"vsBudget":0,"vsForecast":0,"vsForecast2":0}';
     }else{
         $ytd=json_decode($ytdsi);
         $lastmonthselected=  substr(end($post["period"]),0,2); //select the month field only
         $this->data["ytdaverage"]=json_encode(array("period"=>$ytd->period,"Actual"=>round($ytd->Actual/$lastmonthselected,0),"Budget"=>round($ytd->Budget/$lastmonthselected,0),"Forecast"=>round($ytd->Forecast/$lastmonthselected,0),"Forecast2"=>round($ytd->Forecast2/$lastmonthselected,0),"ActualLast"=>round($ytd->ActualLast/$lastmonthselected,0)));
        $this->data["ytdaveragevariance"]=json_encode(array("period"=>$ytd->period,"vsN1"=>round($ytd->Actual/$lastmonthselected,0)-round($ytd->ActualLast/$lastmonthselected,0),"vsBudget"=>round($ytd->Actual/$lastmonthselected,0)-round($ytd->Budget/$lastmonthselected,0),"vsForecast"=>round($ytd->Actual/$lastmonthselected,0)-round($ytd->Forecast/$lastmonthselected,0),"vsForecast2"=>round($ytd->Actual/$lastmonthselected,0)-round($ytd->Forecast2/$lastmonthselected,0)));
               // '{"period":"'.date('Y').'","vsN1":0,"VsBudget":0,"VsForecast":0,"VsForecast2":0}';
     }
     
     /***********8ytg analysis***************/
      //full year N-1
     
      
       $ytdtotal=  json_decode($thisyearsalestotal);
       $groupedtotals=$this->sales_model->getGroupedSalesTotals($data,"SI");
       
    
     
      $customer=$this->input->post("customer");
     if(is_array($customer)){
         $customer=$customer[0];
     }
     else{
         $customer="all";
     }
     if(!$post['datefrom']){
         $data["datefrom"]="01/".date("Y");
     } 
     if(!$post['dateto']){
                  $data["dateto"]="12/".date("Y");
     }
         //post['datefrom'],$post['dateto'],$customer
     if(is_object(json_decode($consolidatedsi))){
     $this->data["siperiodic"]=$consolidatedsi;
     }
     else{
        $this->data["siperiodic"]= '{"period":"01-2019 to 02-2019","Actual":0,"Budget":0,"Forecast":0,"ActualLast":6092}';
     }
     
     $periodicvariance=json_decode($consolidatedsi);
     //die(print_r($periodicvariance));
     $variance["period"]=$periodicvariance->period;
     $variance["vsN1"]=$periodicvariance->Actual-$periodicvariance->ActualLast;
     $variance["vsBudget"]=$periodicvariance->Actual-$periodicvariance->Budget;
     $variance["vsForecast"]=$periodicvariance->Actual-$periodicvariance->Forecast;
     $variance["vsForecast2"]=$periodicvariance->Actual-$periodicvariance->Forecast2;
    // die(print_r(json_encode($variance)));
    
     /*************ytd si**************/
     $variancee=json_decode($ytdsi);
     $ytdvariance["period"]=$variancee->period;
     $ytdvariance["vsN1"]=$variancee->Actual-$variancee->ActualLast;
     $ytdvariance["vsBudget"]=$variancee->Actual-$variancee->Budget;
     $ytdvariance["vsForecast"]=$variancee->Actual-$variancee->Forecast;
      $ytdvariance["vsForecast2"]=$variancee->Actual-$variancee->Forecast2;
   //  die(print_r(json_encode($ytdvariance)));
      $this->data["ytdvariance"]=json_encode($variance);
     $this->data["siperiodicvariance"]=json_encode($variance);
     $this->data["ytdsales"]=$ytdsi;
     $this->data["ytdvariance"]=json_encode($ytdvariance);
     
     //ytd average
     $ytdvariance["period"]=$variancee->period;
     $ytdvariance["vsN1"]=$variancee->Actual-$variancee->ActualLast;
     $ytdvariance["vsBudget"]=$variancee->Actual-$variancee->Budget;
     $ytdvariance["vsForecast"]=$variancee->Actual-$variancee->Forecast;
      $ytdvariance["vsForecast2"]=$variancee->Actual-$variancee->Forecast2;
     
     
     
        if(!is_array(json_decode($salestotal))){
			//die(print_r($salestotal));
             $this->data['salestotal'] ='[{"sale":"PSO","value":"0","color":"#ccc"}]';
             $this->data['psobudget']='[{
    "product": "PSO",
    "actual":0,
   "budget":0,
    "coloractual": "#3bdb23",
    "colorbudget": "#db6023"
  }, {
    "product": "SSO",
    "actual": 0,
    "budget": 0,
    "coloractual": "#3bdb23",
    "colorbudget": "#db6023"
  }]';
        }
        else{
				
      $this->data['salestotal'] = $salestotal;
      $salestt=json_decode($salestotal);
      
      $actualsso=0;
      $actualpso=0;
      $actualsi=0;
      $actualssolastyear=0;
      $actualpsolastyear=0;
                $fullsilastyear=0;

      foreach ($ytdtotal as $tt){
      if($tt->sale=="SSO"){$actualsso=$tt->value;}
      else if($tt->sale=="PSO"){$actualpso=$tt->value;}
      else if($tt->sale=="SI"){$actualsi=$tt->value;}
      }
      
      $lstyeartotal=  json_decode($lastyearsalestotal);
      
      
      
      
      
      //die(print_r($lstyeartotal));
       foreach ($lstyeartotal as $ttl){
      if($ttl->sale=="SSO"){
          $actualssolastyear=$ttl->value;
                }
      else if($ttl->sale=="PSO"){
          $actualpsolastyear=$ttl->value;
             }
             else if($ttl->sale=="SI"){
          $fullsilastyear=$ttl->value;
             }
      }
      
      /**************YTG***************************/
      //FY 
     
      $startyear=$selectedyear;
     $thisyear="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
      $alldates=explode(",",$thisyear);
     foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         $currentyear=  substr($value,-4);
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
         $datemonthsbudget="'".substr($value,-4)."-".substr($value,0,2)."-01',";
         
     }
              $months=  rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
          $datemonthsbudget.=rtrim($datemonthsbudget,",");
       $monthslastyear=  rtrim($monthslastyear,",");
       
       
$budgetfullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"budget","SI");
$forecast1fullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"forecast","SI");
      $forecast2fullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"forecast2","SI");
     
            
       //die($fullsilastyear."sdsdd".$budgetfullyear."dsd".$forecast1fullyear."sdsd".$forecast2fullyear."ffdf".$variancee->Actual);
       
       $remainingmonths=12-$lastmonthselected;
       
       $this->data["ytgmonthlyaverage"]=json_encode(array("period"=>end($post["period"]),"N-1"=>round(($fullsilastyear-$variancee->Actual)/$remainingmonths,2),"Budget"=>round(($budgetfullyear-$variancee->Actual)/$remainingmonths,2),"Forecast1"=>round(($forecast1fullyear-$variancee->Actual)/$remainingmonths,2),"Forecast2"=>round(($forecast2fullyear-$variancee->Actual)/$remainingmonths,2),"Actual"=>round($variancee->Actual/$lastmonthselected,2)));
      /************************YTG************************************/
      $allbudgets=array();
      $budgetsso=array("product"=>"SSO","budget"=>$budgetsalessso,"actual"=>$actualsso,"coloractual"=> "#3bdb23", "colorbudget"=>"#db6023");
      $budgetpso=array("product"=>"PSO","budget"=>$budgetsalespso,"actual"=>$actualpso,"coloractual"=> "#3bdb23", "colorbudget"=>"#db6023");
      array_push($allbudgets, $budgetsso);
      array_push($allbudgets,$budgetpso);
     // die(json_encode($allbudgets));
      $this->data['psobudget']=json_encode($allbudgets);
        }
    }
        
        if(!is_array(json_decode($groupedtotals))){
            
      $this->data['groupedtotals'] ='[{"valuepso":"0","colorpso":"0"}]';
        }else{
            $this->data['groupedtotals'] = $groupedtotals;
        }
        
        if(!is_array(json_decode($groupedqty))){
             $this->data['groupedqty'] ='[{"valuepso":"0","colorpso":"0"}]';
    
        }else{
             $this->data['groupedqty']=$groupedqty;
        }
        
     
      //pso sso actual comparisons
	  if(!isset($actualpso)){
		  $actualpso=0;
	  }
	  
	  
	  if(!isset($actualsso)){
		  $actualsso=0;
	  }
	  
	  
	  if(!isset($actualpsolastyear)){
		  $actualpsolastyear=0;
	  }
	  if(!isset($actualssolastyear)){
		  $actualssolastyear=0;
	  }
	  
     $psossoactual='[{
    "year":'.date("Y").',
    "pso":'.$actualpso.',
     "sso":'.$actualsso.',
     "colorpso": "#3bdb23",
    "colorsso": "#db6023"
  }, {
    "year":"'.$lastyeardate.'",
    "pso":'.$actualpsolastyear.',
    "sso":'.$actualssolastyear.',
    "colorpso": "#3bdb23",
    "colorsso": "#db6023"
  }]';
      $this->data['psossoactual']=$psossoactual;
      
      /*[{"period":"2019-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-08","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-07","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-06","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-05","Actual":"88953.7200","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-04","Actual":"1851376.7300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-03","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-02","Actual":"105402.6300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-01","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-12","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-11","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-10","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0}]*/
      
        $this->data['consolidatedsi']=$this->sales_model->consolidatedSalesSI($data);
      
    
      //die(print_r( $this->data['consolidated']));
      $this->data['remainingmonths']=$remainingmonths;
          $this->data['post']=$post;
      $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales'] = $this->db_model->getLatestSales();
        
      //  $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        $this->data['chatData'] = $this->db_model->getChartData();
        $this->data['stock'] = $this->db_model->getStockValue();
        $this->data['bs'] = $this->db_model->getBestSeller();
        $this->data['filters']=$filters;
        $lmsdate = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        
        $bc = array(array('link' => '#', 'page' => lang('SI Analysis')));
        $meta = array('page_title' => lang('SI Analysis'), 'bc' => $bc);
        $this->page_construct('salesin', $meta, $this->data);
        
    }
    
     function pso(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"datefrom"=>$post['datefrom'],"dateto"=>$post['dateto'],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"period"=>$post["period"],"products"=>$post["products"]);
    $filters="";
    
      if(empty($data["period"])){
        $data=Array ("cluster" => Array ( "0" => "EAH" ), "countrys" => Array ( "0" => 3,"1" => 6, "2" => 73, "3" => 76 ), "gbu" => "all", "promotion" =>"", "productcategoryfamily" =>"", "customer" =>"","grossnet" => 1 ,"ssocountry"=>"ssocountry", "period" => Array (date("m")."-".date("Y") ) );
$_POST["ssocountry"]=$post["ssocountry"]="ssocountry";
$_POST["gsales"]=$post["gsales"]=1;
    }
    
    if($data){
            if(empty($data["period"])){
         $this->session->set_flashdata('error', $this->lang->line("please_select_period_first"));
            redirect($_SERVER["HTTP_REFERER"]);
    }
   foreach ($data as $key=>$value){
       if(is_array($value)){
           if(strtolower($key)=="countrys"){
             foreach ($value as $valuename) {
                $currency=$this->settings_model->getCurrencyByID($valuename);  
                $valuee.=$currency->country.".";
                
               }
              // die($valuee);
               
           }
           else  if(strtolower($key)=="cluster"){
               
             foreach ($value as $valuename) {
                $cluster=$this->settings_model->getClusterByID($valuename);  
                $valuee.=$cluster->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="customer"){
                foreach ($value as $valuename) {
                $company=$this->companies_model->getCompanyByID($valuename);  
                $valuee.=$company->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="productcategoryfamily"){
               foreach ($value as $valuename) {
                $category=$this->products_model->getCategoryById($valuename);  
                $valuee.=$category->name.".";
               }
               
               
           }
           else{
           $valuee.=implode(",",$value);
           }
       }
       else{$valuee=$value;}
       $filters.="&nbsp;<b>".ucwords($key).":</b>".strtoupper($valuee);
   }
    $salestotal=$this->sales_model->getSalesTotals($data);
    //die(print_r($salestotal));
    $lastyeardate=date("Y",strtotime("-1 year"));
    $lastyearsalestotal=  $this->sales_model->getLastYearSalesTotal($data,$lastyeardate);
    $thisyearsalestotal=$this->sales_model->getThisyearSalesTotal($data,date("Y"));
    
    
   
    $consolidatedsi=  $this->sales_model->consolidatedSalesSumPSO($data);
    $ytdpso=$this->sales_model->ytdSales($data,"PSO",date("Y"));
   
      // die(print_r($data));
       $ytdtotal=  json_decode($thisyearsalestotal);
       $groupedtotals=$this->sales_model->getGroupedSalesTotals($data,"PSO");
      
    
     
      $customer=$this->input->post("customer");
     if(is_array($customer)){
         $customer=$customer[0];
     }
     else{
         $customer="all";
     }
     if(!$post['datefrom']){
         $data["datefrom"]="01/".date("Y");
     } 
     if(!$post['dateto']){
                  $data["dateto"]="12/".date("Y");
     }
         //post['datefrom'],$post['dateto'],$customer
     if(is_object(json_decode($consolidatedsi))){
     $this->data["siperiodic"]=$consolidatedsi;
     }
     else{
        $this->data["siperiodic"]= '{"period":"01-2019 to 02-2019","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0}';
     }
     
     $periodicvariance=json_decode($consolidatedsi);
     
     $variance["period"]=$periodicvariance->period;
     $variance["vsN1"]=$periodicvariance->Actual-$periodicvariance->ActualLast;
     $variance["vsBudget"]=$periodicvariance->Actual-$periodicvariance->Budget;
     $variance["vsForecast"]=$periodicvariance->Actual-$periodicvariance->Forecast;
     $variance["vsForecast2"]=$periodicvariance->Actual-$periodicvariance->Forecast2;
     //die(print_r(json_encode($variance)));
     $this->data["psoperiodicvariance"]=  json_encode($variance);
     
     /*********ytd sales************/
      $variancee=json_decode($ytdpso);
     $ytdvariance["period"]=$variancee->period;
     $ytdvariance["vsN1"]=$variancee->Actual-$variancee->ActualLast;
     $ytdvariance["vsBudget"]=$variancee->Actual-$variancee->Budget;
     $ytdvariance["vsForecast"]=$variancee->Actual-$variancee->Forecast;
      $ytdvariance["vsForecast2"]=$variancee->Actual-$variancee->Forecast2;
     //die(print_r(json_encode($variancee)));
     $this->data["ytdvariance"]=  json_encode($ytdvariance);
     $this->data["ytdsales"]=$ytdpso;
     
     //ytg average
     
     
     if(!is_object(json_decode($ytdpso))){
     $this->data["ytdaverage"]='{"period":"'.date('Y').'","Actual":0,"Budget":0,"Forecast":0,"Forecast2":0,"ActualLast":0}';
     $this->data["ytdaveragevariance"]='{"period":"'.date('Y').'","vsN1":0,"vsBudget":0,"vsForecast":0,"vsForecast2":0}';
     }else{
         $ytd=json_decode($ytdpso);
         $months=  substr(end($post["period"]),0,2); //select the month field only
         $this->data["ytdaverage"]=json_encode(array("period"=>$ytd->period,"Actual"=>round($ytd->Actual/$months,0),"Budget"=>round($ytd->Budget/$months,0),"Forecast"=>round($ytd->Forecast/$months,0),"Forecast2"=>round($ytd->Forecast2/$months,0),"ActualLast"=>round($ytd->ActualLast/$months,0)));
        $this->data["ytdaveragevariance"]=json_encode(array("period"=>$ytd->period,"vsN1"=>round($ytd->Actual/$months,0)-round($ytd->ActualLast/$months,0),"vsBudget"=>round($ytd->Actual/$months,0)-round($ytd->Budget/$months,0),"vsForecast"=>round($ytd->Actual/$months,0)-round($ytd->Forecast/$months,0),"vsForecast2"=>round($ytd->Actual/$months,0)-round($ytd->Forecast2/$months,0)));
               // '{"period":"'.date('Y').'","vsN1":0,"VsBudget":0,"VsForecast":0,"VsForecast2":0}';
     }
     
     
     
     
     
        if(!is_array(json_decode($salestotal))){
			//die(print_r($salestotal));
             $this->data['salestotal'] ='[{"sale":"PSO","value":"0","color":"#ccc"}]';
             $this->data['psobudget']='[{
    "product": "PSO",
    "actual":0,
   "budget":0,
    "coloractual": "#3bdb23",
    "colorbudget": "#db6023"
  }, {
    "product": "SSO",
    "actual": 0,
    "budget": 0,
    "coloractual": "#3bdb23",
    "colorbudget": "#db6023"
  }]';
        }
        else{
				
      $this->data['salestotal'] = $salestotal;
      $salestt=json_decode($salestotal);
      
      $actualsso=0;
      $actualpso=0;
      
      $actualssolastyear=0;
      $actualpsolastyear=0;
      foreach ($ytdtotal as $tt){
      if($tt->sale=="SSO"){$actualsso=$tt->value;}
      else if($tt->sale=="PSO"){$actualpso=$tt->value;}
      }
      
      $lstyeartotal=  json_decode($lastyearsalestotal);
      
      //die(print_r($lstyeartotal));
       foreach ($lstyeartotal as $ttl){
      if($ttl->sale=="SSO"){
          $actualssolastyear=$ttl->value;
                }
      else if($ttl->sale=="PSO"){
          $actualpsolastyear=$ttl->value;
             }
      }
    
     
        }
    }
        
        if(!is_array(json_decode($groupedtotals))){
            
      $this->data['groupedtotals'] ='[{"valuepso":"0","colorpso":"0"}]';
        }else{
            $this->data['groupedtotals'] = $groupedtotals;
        }
        
      
        
     
      //pso sso actual comparisons
	  if(!isset($actualpso)){
		  $actualpso=0;
	  }
	  
	  
	  if(!isset($actualsso)){
		  $actualsso=0;
	  }
	  
	  
	  if(!isset($actualpsolastyear)){
		  $actualpsolastyear=0;
	  }
	  if(!isset($actualssolastyear)){
		  $actualssolastyear=0;
	  }
	  
     $psossoactual='[{
    "year":'.date("Y").',
    "pso":'.$actualpso.',
     "sso":'.$actualsso.',
     "colorpso": "#3bdb23",
    "colorsso": "#db6023"
  }, {
    "year":"'.$lastyeardate.'",
    "pso":'.$actualpsolastyear.',
    "sso":'.$actualssolastyear.',
    "colorpso": "#3bdb23",
    "colorsso": "#db6023"
  }]';
      $this->data['psossoactual']=$psossoactual;
      
      /**************YTG***************************/
      //FY 
      $period=end($post["period"]);
    $selectedyear= \substr($period,-4);
   
      $startyear=$selectedyear;
     $thisyear="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
      $alldates=explode(",",$thisyear);
     foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         $currentyear=  substr($value,-4);
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
          $datemonthsbudget="'".substr($value,-4)."-".substr($value,0,2)."-01',";
     }
              $months=  rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
       $monthslastyear=  rtrim($monthslastyear,",");
       $datemonthsbudget=rtrim($datemonthsbudget,",");
       
       
$budgetfullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"budget","PSO");

$forecast1fullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"forecast","PSO");
      $forecast2fullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"forecast2","PSO");
     
            
    //die($actualpso."sdsdd".$variancee->Budget."full year".$budgetfullyear."dsdforecast".$variancee->orecast."Full year".$forecast1fullyear."sdsd".$forecast2fullyear."ffdf".$variancee->Actual);
       $lastmonthselected=  substr(end($post["period"]),0,2); //select the month field only
       $remainingmonths=12-$lastmonthselected;
       
       $this->data["ytgmonthlyaverage"]=json_encode(array("period"=>end($post["period"]),"N-1"=>round(($actualpsolastyear-$variancee->Actual)/$remainingmonths,2),"Budget"=>round(($budgetfullyear-$variancee->Actual)/$remainingmonths,2),"Forecast1"=>round(($forecast1fullyear-$variancee->Actual)/$remainingmonths,2),"Forecast2"=>round(($forecast2fullyear-$variancee->Actual)/$remainingmonths,2),"Actual"=>round($variancee->Actual/$lastmonthselected,2)));
      /************************YTG************************************/
      
      
      /*[{"period":"2019-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-08","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-07","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-06","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-05","Actual":"88953.7200","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-04","Actual":"1851376.7300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-03","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-02","Actual":"105402.6300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-01","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-12","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-11","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-10","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0}]*/
      
        $this->data['consolidatedsi']=$this->sales_model->consolidatedSalesPSO($data);
      
    
      //die(print_r( $this->data['consolidated']));
          $this->data['post']=$post;
      $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales'] = $this->db_model->getLatestSales();
        
      //  $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
       
        $this->data['filters']=$filters;
        $lmsdate = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        
        $bc = array(array('link' => '#', 'page' => lang('PSO Analysis')));
        $meta = array('page_title' => lang('PSO Analysis'), 'bc' => $bc);
        $this->page_construct('pso', $meta, $this->data);
        
    }
       function sso(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"products"=>$post["product"],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"datefrom"=>$post['datefrom'],"dateto"=>$post['dateto'],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"period"=>$post["period"],"products"=>$post["products"]);
   //die(dirname(__FILE__));
        $this->db->cache_off();
    $filters="";
    if(empty($data["period"])){
        $data=Array ("cluster" => Array ("0"=>"EAH"),"period"=>date("m")."-".date("Y"), "countrys" => Array ("3"=>3), "gbu" => "all", "promotion" =>"", "productcategoryfamily" =>"", "customer" =>"","grossnet" => 1 ,"ssocountry"=>"ssocountry", "period" => Array (date("m")."-".date("Y") ) );
$_POST["ssocountry"]=$post["ssocountry"]="ssocountry";
$_POST["gsales"]=$post["gsales"]=1;
    }
   
    if($data){
         if(empty($data["period"])){
         $this->session->set_flashdata('error', $this->lang->line("please_select_period_and_country_first"));
            redirect($_SERVER["HTTP_REFERER"]);
    }
     $lastyeardate=date("Y",strtotime("-1 year"));
     $countries=array();
   foreach ($data as $key=>$value){
       if(is_array($value)){
           if(strtolower($key)=="countrys"){
              
             foreach ($value as $valueid) {
                $currency=$this->settings_model->getCurrencyByID($valueid);  
                             
                
                 array_push($countries,$valueid);
                $valuee.=$currency->country.".";
                
                
               }
     
           }
           else  if(strtolower($key)=="cluster"){
               
             foreach ($value as $valuename) {
                $cluster=$this->settings_model->getClusterByID($valuename);  
                $valuee.=$cluster->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="customer"){
                foreach ($value as $valuename) {
                $company=$this->companies_model->getCompanyByID($valuename);  
                $valuee.=$company->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="productcategoryfamily"){
               foreach ($value as $valuename) {
                $category=$this->products_model->getCategoryById($valuename);  
                $valuee.=$category->name.".";
               }
               
               
           }
           else{
           $valuee.=implode(",",$value);
           }
       }
       else{$valuee=$value;}
       $filters.="&nbsp;<b>".ucwords($key).":</b>".strtoupper($valuee);
   }
   $countrydata=array();
   //get months from year beginning
   $endmonth=end($data["period"]);
   $this->data['selectedyear']=substr($endmonth,-4);
   $this->data["currentmonth"]=$month_name = date("F", mktime(0, 0, 0, substr($endmonth,0,2), 10)); //convert to month name
             $dates=$this->sales_model->getMonthsFromBeginingOfYear($endmonth);
             $alldates=explode(",",$dates["thisyear"]);
              $months="";
               $monthslastyear="";
               
           foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'".$value."',";
         $currentyear=  substr($value,-4);
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
         $datemonthsbudget="'".substr($value,-4)."-".substr($value,0,2)."-01',";
     }
             // $months=  rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
         $datemonthsbudget=rtrim($datemonthsbudget,",");
  // die(print_r($datemonths));
  
  $salestype="SSO";
  //get sso sales per country for whole year and last year
   
         if($post["ssocountry"]=="ssocountry"){
   foreach ($countries as $valueid) {
        $data["countrys"]=array($valueid);
       $salestype="SSO";
    $lastyearsalestotal=  $this->sales_model->getYearSalesTotal($data,$lastyeardate,$salestype);
    $thisyearsalestotal=$this->sales_model->getYearSalesTotal($data,substr(end($data["period"]),-4),$salestype);
    $monthsalestotals=$this->sales_model->getSSOsalesByCountry($data,"'".end($data["period"])."'");
    $lastyearmonth=  substr($data["period"][0],0,2)."-".(substr(end($data["period"]),-4) -1);
    
    $monthsalestotalslastyear=$this->sales_model->getSSOsalesByCountry($data,"'".$lastyearmonth."'");
    //budget
     $countryy["thisyearbudgetmonth"]=$this->budget_model->getBudgetForMonth($salestype,$data,end($data["period"]));
    // $countryy["monthlybudgetlastyear"]=$this->budget_model->getBudgetForMonth($salestype,$data,"'".$lastyearmonth."'");
    
    $currencyy=$this->settings_model->getCurrencyByID($valueid);  
    $countryy["name"]=$currencyy->country;
    $countryy["periodicsales"]=$this->sales_model->getSalesSSOSalesPerCountry($valueid,$data);
    $countryy["lastyearsales"]=$lastyearsalestotal;
    $countryy["thisyearsales"]=$thisyearsalestotal;
     $countryy["lastyearsalesmonth"]=round($monthsalestotalslastyear->resale/1000,0);
     $countryy["thisyearsalesmonth"]=round($monthsalestotals->resale/1000,0);
    $countryy["thisyearsales"]=$thisyearsalestotal;
   //ytd
    $countryy["ytdsalesthisyear"]=$this->sales_model->ytdSalesSalesType($data,"SSO"); //check for last year
    //copy search array bcz of changing dates
    $dataa=$data;
    $dataa["period"]=array($lastyearmonth);
    //die(print_r($dataa));
    $countryy["ytdsaleslastyear"]=$this->sales_model->ytdSalesSalesType($dataa,"SSO");
    $budgetlast=$this->sales_model->getYearBudgetTotal($data,$lastyeardate,"SSO","budget");
       $countryy["lastyearbudget"]=$budgetlast;
   $budgetcurrent= $this->sales_model->getYearBudgetTotal($data,substr(end($data["period"]),-4),"SSO","budget");
    $countryy["thisyearbudget"]=$budgetcurrent;
          $countryy["budgetytd"]=  $this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"budget","SSO");
    //ytg
          $countryy["ytgsaleslastyear"]=$this->sales_model->ytgSalesSalesType($dataa,"SSO");
          $countryy["ytgsalesthisyear"]=$budgetcurrent-$countryy["ytdsalesthisyear"];
          //get remaining months and calculate ytg
         
        $countryy["budgetytg"]=  $this->sales_model->ytgBudget($data,$datemonths,"budget","SSO");
          
    array_push($countrydata, $countryy);
   }
   
         }
         //if sso product option
         else if($post["ssocountry"]=="ssoproduct"){
             
        if(empty($data["productcategoryfamily"])|| in_array("all",$data["productcategoryfamily"])){
            
            //iterate through all brands
            $categories=$this->settings_model->getAllCategories($data);   
            
            foreach ($categories as $cat) {
                //check promotion
                if(empty($post["promotion"])|| $post["promotion"]=="all"){
                    $promotion=TRUE;
                } else{
                $promotion=$this->settings_model->getPromotedProductCategory($cat->id,$post['promotion']);
                }
                if($promotion){
                    
          $data["productcategoryfamily"]=array($cat->id);
        $lastyearsalestotal=  $this->sales_model->getYearSalesTotal($data,$lastyeardate,$salestype);
    $thisyearsalestotal=$this->sales_model->getYearSalesTotal($data,substr(end($data["period"]),-4),$salestype);
    $monthsalestotals=$this->sales_model->getSSOsalesByCountry($data,"'".end($data["period"])."'");
    $lastyearmonth=  substr($data["period"][0],0,2)."-".(substr(end($data["period"]),-4) -1);
    
      $monthsalestotalslastyear=$this->sales_model->getSSOsalesByCountry($data,"'".$lastyearmonth."'");
    //budget
     $countryy["thisyearbudgetmonth"]=$this->budget_model->getBudgetForMonth($salestype,$data,end($data["period"]));
    // $countryy["monthlybudgetlastyear"]=$this->budget_model->getBudgetForMonth($salestype,$data,"'".$lastyearmonth."'");
    
    $countryy["id"]=$cat->id;
    $countryy["name"]=$cat->name;
    $countryy["periodicsales"]=$this->sales_model->getSalesSSOSalesPerCountry($valueid,$data);
    $countryy["lastyearsales"]=$lastyearsalestotal;
    $countryy["thisyearsales"]=$thisyearsalestotal;
     $countryy["lastyearsalesmonth"]=round($monthsalestotalslastyear->resale/1000,0);
     $countryy["thisyearsalesmonth"]=round($monthsalestotals->resale/1000,0);
    $countryy["thisyearsales"]=$thisyearsalestotal;
   //ytd
    $countryy["ytdsalesthisyear"]=$this->sales_model->ytdSalesSalesType($data,"SSO"); //check for last year
    //copy search array bcz of changing dates
    $dataa=$data;
    $dataa["period"]=array($lastyearmonth);
    //die(print_r($dataa));
    $countryy["ytdsaleslastyear"]=$this->sales_model->ytdSalesSalesType($dataa,"SSO");
    $budgetlast=$this->sales_model->getYearBudgetTotal($data,$lastyeardate,"SSO","budget");
       $countryy["lastyearbudget"]=$budgetlast;
   $budgetcurrent= $this->sales_model->getYearBudgetTotal($data,substr(end($data["period"]),-4),"SSO","budget");
    $countryy["thisyearbudget"]=$budgetcurrent;
          $countryy["budgetytd"]=  $this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"budget","SSO");
    //ytg
          $countryy["ytgsaleslastyear"]=$this->sales_model->ytgSalesSalesType($dataa,"SSO");
          $countryy["ytgsalesthisyear"]=$budgetcurrent-$countryy["ytdsalesthisyear"];
          //get remaining months and calculate ytg
         
        $countryy["budgetytg"]=  $this->sales_model->ytgBudget($data,$datemonths,"budget","SSO");
          
    array_push($countrydata, $countryy);
                
            }
            } 
            
        }
        else {
            //iterate through chosen ones
            
            foreach ($data["productcategoryfamily"] as $cat) {
                $catt=$this->settings_model->getCategoryByID($cat);
                if(empty($post["promotion"])|| $post["promotion"]=="all"){
                    $promotion=TRUE;
                } else{
                 $promotion=$this->settings_model->getPromotedProductCategory($catt->id,$post['promotion']);
                }
                
                if($promotion ){
                       $data["productcategoryfamily"]=array($catt->id);
                $lastyearsalestotal=  $this->sales_model->getYearSalesTotal($data,$lastyeardate,$salestype);
    $thisyearsalestotal=$this->sales_model->getYearSalesTotal($data,substr(end($data["period"]),-4),$salestype);
    $monthsalestotals=$this->sales_model->getSSOsalesByCountry($data,"'".end($data["period"])."'");
    $lastyearmonth=  substr($data["period"][0],0,2)."-".(substr(end($data["period"]),-4) -1);
    
      $monthsalestotalslastyear=$this->sales_model->getSSOsalesByCountry($data,"'".$lastyearmonth."'");
    //budget
     $countryy["thisyearbudgetmonth"]=$this->budget_model->getBudgetForMonth($salestype,$data,end($data["period"]));
    // $countryy["monthlybudgetlastyear"]=$this->budget_model->getBudgetForMonth($salestype,$data,"'".$lastyearmonth."'");
    
    $countryy["id"]=$cat;
    $countryy["name"]=$catt->name;
    $countryy["periodicsales"]=$this->sales_model->getSalesSSOSalesPerCountry($valueid,$data);
    $countryy["lastyearsales"]=$lastyearsalestotal;
    $countryy["thisyearsales"]=$thisyearsalestotal;
     $countryy["lastyearsalesmonth"]=round($monthsalestotalslastyear->resale/1000,0);
     $countryy["thisyearsalesmonth"]=round($monthsalestotals->resale/1000,0);
    $countryy["thisyearsales"]=$thisyearsalestotal;
   //ytd
    $countryy["ytdsalesthisyear"]=$this->sales_model->ytdSalesSalesType($data,"SSO"); //check for last year
    //copy search array bcz of changing dates
    $dataa=$data;
    $dataa["period"]=array($lastyearmonth);
    //die(print_r($dataa));
    $countryy["ytdsaleslastyear"]=$this->sales_model->ytdSalesSalesType($dataa,"SSO");
    $budgetlast=$this->sales_model->getYearBudgetTotal($data,$lastyeardate,"SSO","budget");
       $countryy["lastyearbudget"]=$budgetlast;
   $budgetcurrent= $this->sales_model->getYearBudgetTotal($data,substr(end($data["period"]),-4),"SSO","budget");
    $countryy["thisyearbudget"]=$budgetcurrent;
          $countryy["budgetytd"]=  $this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"budget","SSO");
    //ytg
          $countryy["ytgsaleslastyear"]=$this->sales_model->ytgSalesSalesType($dataa,"SSO");
          $countryy["ytgsalesthisyear"]=$budgetcurrent-$countryy["ytdsalesthisyear"];
          //get remaining months and calculate ytg
         
        $countryy["budgetytg"]=  $this->sales_model->ytgBudget($data,$datemonths,"budget","SSO");
          
    array_push($countrydata, $countryy);
                
                
            }
            
            } 
            
        }
             
             
         }
         else if($post["ssocountry"]=="monthlytrend"){
             //monthly trend
         }
         else {
             //anyother 
             
         }
   
    $this->data["reminingmonths"]=12-substr(end($data["period"]),0,2);
          
 //die(print_r($countrydata));
   $this->data["countries"]=$countrydata;
   $countrybrands=array_column($countrydata,"name");
  //get name of search paramaters ie countries or brands 
   $this->data["countrybrandnames"]= $countrybrands;
   $this->data["table"]=$post["ssocountry"];
   
   $data["countrys"]=$post['f_country'];
    $salestotal=$this->sales_model->getSalesTotals($data);
    //die(print_r($salestotal));
   
    $lastyearsalestotal=  $this->sales_model->getLastYearSalesTotal($data,$lastyeardate);
    $thisyearsalestotal=$this->sales_model->getThisyearSalesTotal($data,date("Y"));
  // die(print_r($data));
    $consolidatedsi=  $this->sales_model->consolidatedSalesSumSSO($data);
    $ytdpso=$this->sales_model->ytdSales($data,"SSO",date("Y"));
   
     //  die(print_r($consolidatedsi));
       $ytdtotal=  json_decode($thisyearsalestotal);
       $groupedtotals=$this->sales_model->getGroupedSalesTotals($data,"SSO");
      
    
     
      $customer=$this->input->post("customer");
     if(is_array($customer)){
         $customer=$customer[0];
     }
     else{
         $customer="all";
     }
     if(!$post['datefrom']){
         $data["datefrom"]="01/".date("Y");
     } 
     if(!$post['dateto']){
                  $data["dateto"]="12/".date("Y");
     }
         //post['datefrom'],$post['dateto'],$customer
     if(is_object(json_decode($consolidatedsi))){
     $this->data["siperiodic"]=$consolidatedsi;
     }
     else{
        $this->data["siperiodic"]= '{"period":"01-2019 to 02-2019","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0}';
     }
     
     $periodicvariance=json_decode($consolidatedsi);
     
     $variance["period"]=$periodicvariance->period;
     $variance["vsN1"]=$periodicvariance->Actual-$periodicvariance->ActualLast;
     $variance["vsBudget"]=$periodicvariance->Actual-$periodicvariance->Budget;
     $variance["vsForecast"]=$periodicvariance->Actual-$periodicvariance->Forecast;
     $variance["vsForecast2"]=$periodicvariance->Actual-$periodicvariance->Forecast2;
     //die(print_r(json_encode($variance)));
     $this->data["psoperiodicvariance"]=  json_encode($variance);
     
     /*********ytd sales************/
      $variancee=json_decode($ytdpso);
     $ytdvariance["period"]=$variancee->period;
     $ytdvariance["vsN1"]=$variancee->Actual-$variancee->ActualLast;
     $ytdvariance["vsBudget"]=$variancee->Actual-$variancee->Budget;
     $ytdvariance["vsForecast"]=$variancee->Actual-$variancee->Forecast;
     $ytdvariance["vsForecast2"]=$variancee->Actual-$variancee->Forecast2;
     //die(print_r(json_encode($variancee)));
     $this->data["ytdvariance"]=  json_encode($ytdvariance);
     $this->data["ytdsales"]=$ytdpso;
     
     if(!is_object(json_decode($ytdpso))){
     $this->data["ytdaverage"]='{"period":"'.date('Y').'","Actual":0,"Budget":0,"Forecast":0,"Forecast2":0,"ActualLast":0}';
     $this->data["ytdaveragevariance"]='{"period":"'.date('Y').'","vsN1":0,"vsBudget":0,"vsForecast":0,"vsForecast2":0}';
     }else{
         $ytd=json_decode($ytdpso);
         $months=  substr(end($post["period"]),0,2); //select the month field only
         $this->data["ytdaverage"]=json_encode(array("period"=>$ytd->period,"Actual"=>round($ytd->Actual/$months,0),"Budget"=>round($ytd->Budget/$months,0),"Forecast"=>round($ytd->Forecast/$months,0),"Forecast2"=>round($ytd->Forecast2/$months,0),"ActualLast"=>round($ytd->ActualLast/$months,0)));
        $this->data["ytdaveragevariance"]=json_encode(array("period"=>$ytd->period,"vsN1"=>round($ytd->Actual/$months,0)-round($ytd->ActualLast/$months,0),"vsBudget"=>round($ytd->Actual/$months,0)-round($ytd->Budget/$months,0),"vsForecast"=>round($ytd->Actual/$months,0)-round($ytd->Forecast/$months,0),"vsForecast2"=>round($ytd->Actual/$months,0)-round($ytd->Forecast2/$months,0)));
               // '{"period":"'.date('Y').'","vsN1":0,"VsBudget":0,"VsForecast":0,"VsForecast2":0}';
     }
     
        if(!is_array(json_decode($salestotal))){
			//die(print_r($salestotal));
             $this->data['salestotal'] ='[{"sale":"SSO","value":"0","color":"#ccc"}]';
             $this->data['psobudget']='[{"product": "SSO","actual":0,"budget":0,"coloractual": "#3bdb23","colorbudget": "#db6023"}, {"product": "SSO","actual": 0,"budget": 0,
    "coloractual": "#3bdb23",
    "colorbudget": "#db6023"
  }]';
        }
        else{
				
      $this->data['salestotal'] = $salestotal;
      $salestt=json_decode($salestotal);
      
      $actualsso=0;
      $actualpso=0;
      
      $actualssolastyear=0;
      $actualpsolastyear=0;
      foreach ($ytdtotal as $tt){
      if($tt->sale=="SSO"){$actualsso=$tt->value;}
      else if($tt->sale=="SSO"){$actualpso=$tt->value;}
      }
      
      $lstyeartotal=  json_decode($lastyearsalestotal);
      
      //die(print_r($lstyeartotal));
       foreach ($lstyeartotal as $ttl){
      if($ttl->sale=="SSO"){
          $actualssolastyear=$ttl->value;
                }
      else if($ttl->sale=="SSO"){
          $actualpsolastyear=$ttl->value;
             }
      }
    
     
        }
    }
        
        if(!is_array(json_decode($groupedtotals))){
            
      $this->data['groupedtotals'] ='[{"valuepso":"0","colorpso":"0"}]';
        }else{
            $this->data['groupedtotals'] = $groupedtotals;
        }
        
       
        
     
      //pso sso actual comparisons
	  if(!isset($actualpso)){
		  $actualpso=0;
	  }
	  
	  
	  if(!isset($actualsso)){
		  $actualsso=0;
	  }
	  
	  
	  if(!isset($actualpsolastyear)){
		  $actualpsolastyear=0;
	  }
	  if(!isset($actualssolastyear)){
		  $actualssolastyear=0;
	  }
	  
     $psossoactual='[{
    "year":'.date("Y").',
    "pso":'.$actualpso.',
     "sso":'.$actualsso.',
     "colorpso": "#3bdb23",
    "colorsso": "#db6023"
  }, {
    "year":"'.$lastyeardate.'",
    "pso":'.$actualpsolastyear.',
    "sso":'.$actualssolastyear.',
    "colorpso": "#3bdb23",
    "colorsso": "#db6023"
  }]';
     
     /**************YTG***************************/
      //FY 
     $period=end($post["period"]);
    $selectedyear= \substr($period,-4);
    $lastmonthselected=  substr($period,0,2); //select the month field only
       $remainingmonths=12-$lastmonthselected;
      $startyear=$selectedyear;
     $thisyear="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
      $alldates=explode(",",$thisyear);
     foreach ($alldates as $value) {
         
         $months.="'".$value."',";
           $datemonths.="'01-".$value."',";
         $currentyear=  substr($value,-4);
         $lastyear=$currentyear-1;
      //   $monthscurrentyearmonth.="'".$currentyear."-".substr($value,0,2)."',";
         $monthslastyear.="'".substr($value,0,2)."-".$lastyear."',";
          $datemonthsbudget="'".substr($value,-4)."-".substr($value,0,2)."-01',";
     }
              $months=  rtrim($months,",");
         $datemonths.=rtrim($datemonths,",");
         $datemonthsbudget.=rtrim($datemonthsbudget,",");
       $monthslastyear=  rtrim($monthslastyear,",");
       
       
$budgetfullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"budget","SSO");
$forecast1fullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"forecast","SSO");
      $forecast2fullyear=$this->sales_model->getBudgetForecastForMonth($data,$datemonthsbudget,"forecast2","SSO");
     
      //countries
    
      
      
      
       //die($fullsilastyear."sdsdd".$budgetfullyear."dsd".$forecast1fullyear."sdsd".$forecast2fullyear."ffdf".$variancee->Actual);
       
         
       $this->data["ytgmonthlyaverage"]=json_encode(array("period"=>end($post["period"]),"N-1"=>round(($actualssolastyear-$variancee->Actual)/$remainingmonths,2),"Budget"=>round(($budgetfullyear-$variancee->Actual)/$remainingmonths,2),"Forecast1"=>round(($forecast1fullyear-$variancee->Actual)/$remainingmonths,2),"Forecast2"=>round(($forecast2fullyear-$variancee->Actual)/$remainingmonths,2),"Actual"=>round($variancee->Actual/$lastmonthselected,2)));
      /************************YTG************************************/
     
     
     
      $this->data['psossoactual']=$psossoactual;
      
      /*[{"period":"2019-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-08","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-07","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-06","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-05","Actual":"88953.7200","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-04","Actual":"1851376.7300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-03","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-02","Actual":"105402.6300","Budget":0,"Forecast":0,"ActualLast":0},{"period":"2019-01","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-12","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-11","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-10","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0},{"period":"2018-09","Actual":0,"Budget":0,"Forecast":0,"ActualLast":0}]*/
      
        $this->data['consolidatedsi']=$this->sales_model->consolidatedSalesSSO($data);
        $this->data['remainingmonths']=$remainingmonths;
    
      //die(print_r( $this->data['consolidated']));
      $this->data['post']=$post;
      $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales'] = $this->db_model->getLatestSales();
        
      //  $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
       
        $this->data['filters']=$filters;
        $lmsdate = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        
        $bc = array(array('link' => '#', 'page' => lang('SSO Analysis')));
        $meta = array('page_title' => lang('SSO Analysis'), 'bc' => $bc);
        $this->page_construct('sso', $meta, $this->data);
        
    }
    
    function adhoc(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"datefrom"=>$post['datefrom'],"dateto"=>$post['dateto'],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"products"=>$post["products"]);
    $filters="";
   foreach ($data as $key=>$value){
       if(is_array($value)){
           if(strtolower($key)=="countrys"){
             foreach ($value as $valuename) {
                $currency=$this->settings_model->getCurrencyByID($valuename);  
                $valuee.=$currency->country.".";
                
               }
              // die($valuee);
               
           }
           else  if(strtolower($key)=="cluster"){
               
               foreach ($value as $valuename) {
                $cluster=$this->settings_model->getClusterByID($valuename);  
                $valuee.=$cluster->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="customer"){
                foreach ($value as $valuename) {
                $company=$this->companies_model->getCompanyByID($valuename);  
                $valuee.=$company->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="productcategoryfamily"){
               foreach ($value as $valuename) {
                $category=$this->products_model->getCategoryById($valuename);  
                $valuee.=$category->name.".";
               }
               
               
           }
           else{
           $valuee.=implode(",",$value);
           }
       }
       else{$valuee=$value;}
       $filters.="&nbsp;<b>".ucwords($key).":</b>".strtoupper($valuee);
   }
   
    $this->data['bestcustomerspso'] = $this->db_model->getBestSellingCustomers($data,"pso");
           $this->data['bestcustomerssso'] = $this->db_model->getBestSellingCustomers($data,"sso");
     
    
     
      $customer=$this->input->post("customer");
     if(is_array($customer)){
         $customer=$customer[0];
     }
     else{
         $customer="all";
     }
     if(!$post['datefrom']){
         $data["datefrom"]="01/".date("Y");
     } 
     if(!$post['dateto']){
                  $data["dateto"]="12/".date("Y");
     }
         //post['datefrom'],$post['dateto'],$customer
     
     
   
        
          $bestsellingpso=$this->sales_model->bestsellingproducts($data,$salestype="PSO");
     //die(print_r($bestsellingpso));
      $bestsellingsso=$this->sales_model->bestsellingproducts($data,$salestype="SSO");
      
     
      if(!is_array(json_decode($bestsellingpso))){
          
      $this->data['bestselling']='[{"product":"No Product","SoldQty":"0"}]';
      } else{
      $this->data['bestselling']=$bestsellingpso;
      }
      
      if(!is_array(json_decode($bestsellingsso))){
      $this->data['bestsellingsso']='[{"product":"No Product","SoldQty":"0"}]';
      }
      else{
          $this->data['bestsellingsso']=$bestsellingsso;
          
      }
     
     
      
      $consolidated=$this->sales_model->consolidatedSalesFamily($data);
      $this->data['post']=$post;
      $this->data['consolidatedfamily']=$consolidated;
      //die(print_r( $this->data['consolidated']));
           $this->data['bestcustomerspso'] = $this->db_model->getBestSellingCustomers($data,$scenario="PSO");
		    $this->data['bestcustomerssso'] = $this->db_model->getBestSellingCustomers($data,$scenario="SSO");
      $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales'] = $this->db_model->getLatestSales();
        
      //  $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        $this->data['chatData'] = $this->db_model->getChartData();
        $this->data['stock'] = $this->db_model->getStockValue();
        $this->data['bs'] = $this->db_model->getBestSeller();
        $this->data['filters']=$filters;
        $lmsdate = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs'] = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc = array(array('link' => '#', 'page' => lang('Ad Hoc Analysis')));
        $meta = array('page_title' => lang('Ad Hoc Analysis'), 'bc' => $bc);
        $this->page_construct('adhoc', $meta, $this->data);
        
    }
    
    function monthlytrend(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"period"=>$post["period"],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"products"=>$post["products"]);
    $filters="";
        $this->db->cache_off();
     if(empty($data["period"])){
        $data=Array ("cluster" => Array (), "countrys" => Array (), "gbu" => "all", "promotion" =>"", "productcategoryfamily" =>"", "customer" =>"","grossnet" => 1 ,"ssocountry"=>"ssocountry", "period" => date("Y") );
$_POST["ssocountry"]=$post["ssocountry"]="ssocountry";
$_POST["gsales"]=$post["gsales"]=1;
    }
   foreach ($data as $key=>$value){
       if(is_array($value)){
           if(strtolower($key)=="countrys"){
             foreach ($value as $valuename) {
                $currency=$this->settings_model->getCurrencyByID($valuename);  
                $valuee.=$currency->country.".";
                
               }
              // die($valuee);
               
           }
           else  if(strtolower($key)=="cluster"){
               
               foreach ($value as $valuename) {
                $cluster=$this->settings_model->getClusterByID($valuename);  
                $valuee.=$cluster->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="customer"){
                foreach ($value as $valuename) {
                $company=$this->companies_model->getCompanyByID($valuename);  
                $valuee.=$company->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="productcategoryfamily"){
               foreach ($value as $valuename) {
                $category=$this->products_model->getCategoryById($valuename);  
                $valuee.=$category->name.".";
               }
               
               
           }
           else{
           $valuee.=implode(",",$value);
           }
       }
       else{$valuee=$value;}
       $filters.="&nbsp;<b>".ucwords($key).":</b>".strtoupper($valuee);
   }
   
   //calculate months based on year selected
  
   $startyear=$data["period"];
   $lastyear=$startyear-1;
   
  $thisyeardates="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
  $dates["allyears"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear.",".$thisyeardates;    
  $alldates=explode(",",$dates["allyears"]);
 
  //calculates sales for each month for each brand
  $countrydata=array();
 if(empty($data["productcategoryfamily"])|| in_array("all",$data["productcategoryfamily"])){
            //iterate through all brands
            $categories=$this->settings_model->getAllCategories($data);     
            foreach ($categories as $cat) {
                if(empty($post["promotion"])|| $post["promotion"]=="all"){
                    $promotion=TRUE;
                } else{
                 $promotion=$this->settings_model->getPromotedProductCategory($cat->id,$post['promotion']);
                }
                
                if($promotion ){
          $data["productcategoryfamily"]=array($cat->id);

    $countryy["id"]=$cat->id;
    $countryy["name"]=$cat->name;
    foreach ($alldates as $datemonth) {
        $sale=$this->sales_model->getSSOsalesByCountry($data,"'".$datemonth."'");
      $monthsalestotals[$datemonth]=round($sale->resale/1000); 
      
    }
 
    
$countryy["salestotals"]=$monthsalestotals;
          
    array_push($countrydata, $countryy);
                }
            }
            
            
        }
        else {
            //iterate through chosen ones
            
            foreach ($data["productcategoryfamily"] as $cat) {
                $catt=$this->settings_model->getCategoryByID($cat);
                if(empty($post["promotion"])|| $post["promotion"]=="all"){
                    $promotion=TRUE;
                } else{
                 $promotion=$this->settings_model->getPromotedProductCategory($catt->id,$post['promotion']);
                }
                
                if($promotion ){
                       $data["productcategoryfamily"]=array($catt->id);
              
     foreach ($alldates as $datemonth) {
         $sale=$this->sales_model->getSSOsalesByCountry($data,"'".$datemonth."'");
      $monthsalestotals[$datemonth]=round($sale->resale/1000);  
    }
 
    
$countryy["salestotals"]=$monthsalestotals;
    
    $countryy["name"]=$catt->name;
      $countryy["id"]=$cat;
          
    array_push($countrydata, $countryy);
                
    
                }
                
            }
            
            
            
        }
        
        //end month sales totals
 
  $this->data["countries"]=$countrydata;
  $this->data["alldates"]=$alldates;
   $this->data["yearonly"]=$startyear;
    $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['filters']=$filters;
        
       
        $bc = array(array('link' => '#', 'page' => lang('Monthly trend analysis-SSO')));
        $meta = array('page_title' => lang('Monthly trend analysis'), 'bc' => $bc);
        $this->page_construct('monthlytrend', $meta, $this->data);
        
    }
    
     function sit(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"period"=>$post["period"],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"products"=>$post["products"]);
    $filters="";
        $this->db->cache_off();
     if(empty($data["period"])){
        $data=Array ("cluster" => Array ( "0" => "EAH" ), "countrys" => Array ( "0" => 3,"1" => 6, "2" => 73, "3" => 76 ), "gbu" => "all", "promotion" =>"", "productcategoryfamily" =>"", "customer" =>"","grossnet" => 1 ,"ssocountry"=>"ssocountry", "period" => date("Y") );
$_POST["ssocountry"]=$post["ssocountry"]="ssocountry";
$_POST["gsales"]=$post["gsales"]=1;
    }
   foreach ($data as $key=>$value){
       if(is_array($value)){
           if(strtolower($key)==="countrys"){
             foreach ($value as $valuename) {
                $currency=$this->settings_model->getCurrencyByID($valuename);  
                $valuee.=$currency->country.".";
                
               }
              // die($valuee);
               
           }
           else  if(strtolower($key)=="cluster"){
               
               foreach ($value as $valuename) {
                $cluster=$this->settings_model->getClusterByID($valuename);  
                $valuee.=$cluster->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="customer"){
                foreach ($value as $valuename) {
                $company=$this->companies_model->getCompanyByID($valuename);  
                $valuee.=$company->name.".";
               }
               
               
           }
            else  if(strtolower($key)=="productcategoryfamily"){
               foreach ($value as $valuename) {
                $category=$this->products_model->getCategoryById($valuename);  
                $valuee.=$category->name.".";
               }
               
               
           }
           else{
           $valuee.=implode(",",$value);
           }
       }
       else{$valuee=$value;}
       $filters.="&nbsp;<b>".ucwords($key).":</b>".strtoupper($valuee);
   }
   
   //calculate months based on year selected
   $startyear=$data["period"];
   $lastyear=$startyear-1;
   
  $thisyeardates="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
  $dates["allyears"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear.",".$thisyeardates;    
  $alldates=explode(",",$dates["allyears"]);
 
  //calculates sales for each month for each brand
  $countrydata=array();
$allgraphs=array();
            //iterate through chosen ones
  $salesitems=array("PSO","SSO","Stock","Month_Cvr");
            
            foreach ($salesitems as $cat) {
               
              
     foreach ($alldates as $datemonth) {
         
         if ($cat=="PSO"){
             //ok
             $psosale=$this->sales_model->getPSOsalesByCountry($data,"'".$datemonth."'"); 
              
            $monthsalestotals[$datemonth]=$psosale;   
         }
         
         else if($cat=="SSO"){
             //ok
            $sale= $this->sales_model->getSSOsalesByCountry($data,"'".$datemonth."'");
            $ssosale=round($sale->resale/1000);
         
      $monthsalestotals[$datemonth]=$ssosale;
         }
          else if ($cat=="Stock"){
              $stock=$this->purchases_model->getClosingStock($data,$datemonth,"SSO");
             
            $monthsalestotals[$datemonth]=$stock;    
         }
          else if ($cat=="Month_Cvr"){
              $stockcover=$this->purchases_model->getMonthStockCover($data,$datemonth,"SSO");
              
            $monthsalestotals[$datemonth]=$stockcover;    
         }
           
    }
 
    
$countryy["salestotals"]=$monthsalestotals;
    
    $countryy["name"]=$cat;
      
          
    array_push($countrydata, $countryy);
                
                
            }
            
             foreach ($alldates as $datemonth) {
                 $month_name = date("F", mktime(0, 0, 0, substr($datemonth,0,2), 10));
         $graph["Date"]=substr($month_name,0,3)."-".substr($datemonth,-4);
        
             
         
             //ok
             $psosale=$this->sales_model->getPSOsalesByCountry($data,"'".$datemonth."'"); 
              $graph["PSO"]=$psosale;
              
              //ok
            $sale= $this->sales_model->getSSOsalesByCountry($data,"'".$datemonth."'");
            $ssosale=round($sale->resale/1000);
            $graph["SSO"]=$ssosale;
            $graph["month"]=$datemonth;
            
              $stock=$this->purchases_model->getClosingStock($data,$datemonth,"SSO");
            //  echo $datemonth."fdfd".$stock."fdf";
              $graph["Stock"]=$stock;
             
         
              $stockcover=$this->purchases_model->getMonthStockCover($data,$datemonth,"SSO");
              $graph["Month_Cvr"]=$stockcover;
         
       array_push($allgraphs,$graph);     
    }
     //die(print_r($allgraphs));       
//     [{"Date":"12/2019","SSO":13.80,"PSO":11.27,"Stock":8.75,"Month_Cvr":12.30,"Index V":10.60,"Index VI":14.42},
//{"Date":"01-2020","SSO":15.49,"PSO":14.05,"Stock":6.26,"Month_Cvr":13.37,"Index V":9.25,"Index VI":18.92},
//{"Date":"02-2020","SSO":16.51,"PSO":18.77,"Stock":3.22,"Month_Cvr":18.02,"Index V":11.02,"Index VI":24.78},
//{"Date":"03-2020","SSO":20.36,"PSO":22.35,"Stock":4.74,"Month_Cvr":22.93,"Index V":7.69,"Index VI":20.99},
//{"Date":"04-2020","SSO":25.64,"PSO":24.02,"Stock":1.09,"Month_Cvr":19.08,"Index V":13.39,"Index VI":25.43},
//{"Date":"05-2020","SSO":22.21,"PSO":21.83,"Stock":6.15,"Month_Cvr":23.07,"Index V":16.72,"Index VI":23.14},
//{"Date":"06-2020","SSO":18.81,"PSO":23.23,"Stock":5.21,"Month_Cvr":19.67,"Index V":13.97,"Index VI":25.71},
//{"Date":"07-2020","SSO":21.98,"PSO":19.23,"Stock":11.10,"Month_Cvr":17.73,"Index V":14.70,"Index VI":31.29},
//{"Date":"08-2020","SSO":18.10,"PSO":25.12,"Stock":7.33,"Month_Cvr":19.89,"Index V":20.07,"Index VI":36.57},
//{"Date":"09-2020","SSO":18.30,"PSO":25.56,"Stock":13.22,"Month_Cvr":17.05,"Index V":16.68,"Index VI":39.10},
//{"Date":"10-2020","SSO":22.17,"PSO":25.02,"Stock":16.85,"Month_Cvr":15.03,"Index V":19.65,"Index VI":43.62},
//{"Date":"11-2020","SSO":22.74,"PSO":25.02,"Stock":18.35,"Month_Cvr":12.48,"Index V":24.32,"Index VI":43.62}]       
            
      
        
        //end month sales totals
   // die(print_r($allgraphs));
  $this->data["countries"]=$countrydata;
  $this->data["sitanalysis"]=  json_encode($allgraphs);
  $this->data["alldates"]=$alldates;
   $this->data["yearonly"]=$startyear;
    $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['filters']=$filters;
        
       
        $bc = array(array('link' => '#', 'page' => lang('PSO-SSO-&-SIT_Analyis')));
        $meta = array('page_title' => lang('PSO-SSO-&-SIT_Analyis'), 'bc' => $bc);
        $this->page_construct('sit', $meta, $this->data);
        
    }
    
    
     function distributorsit(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"period"=>$post["period"],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"products"=>$post["products"]);
    $filters="";
        $this->db->cache_off();
    //die(print_r($data));
   // die(print_r($data));
     if(empty($data["period"])){
        $data=Array ("cluster" => Array ( "0" => "EAH" ), "countrys" => Array (0=>3,1=>6,2=>76), "gbu" => "all", "promotion" =>"", "productcategoryfamily" =>"", "customer" =>"","grossnet" => 1 ,"ssocountry"=>"ssocountry", "period" => date("m-Y") );
$_POST["ssocountry"]=$post["ssocountry"]="ssocountry";
$_POST["gsales"]=$post["gsales"]=1;
    }
    //getStockPerCounntryperBrandPerCustomerfordate
 
    $this->db->cache_off();
     if(empty($data["productcategoryfamily"])|| in_array("all",$data["productcategoryfamily"])){
           $categories=$this->settings_model->getAllCategories($data); 
            if($data["gbu"] !="all"){
                foreach ($categories as $value) {
                if($value->id && strtolower($value->gbu)==  strtolower($data["gbu"])){
                 $data['productcategoryfamily'][]=$value->id;
                }
            }
                
            }
            else{
            //iterate through all brands
           
            foreach ($categories as $value) {
                if($value->id){
                 $data['productcategoryfamily'][]=$value->id;
                }
            }
            }
     }
     
  //  die(print_r($data['productcategoryfamily']));
    $graphdata=array();
    
    if(empty($data["products"])|| in_array("all",$data["products"])){
        $productsarray=  $this->products_model->getAllProducts();
        if($data["promotion"] !="all" && !empty($data["promotion"])){
        foreach ($productsarray as $value) {
            if($value->promoted==$data["promotion"]){
            $products[]=$value->id;
            }
        }
        } else{
             foreach ($productsarray as $value) {
            
            $products[]=$value->id;
        }
        }
    }else{
       // die(print_r($data["products"]));    
        $products=$data["products"];
    }
    
    if(empty($data["customer"])|| in_array("all",$data["customer"])){
        $this->db->where(array("group_name"=>"customer"));
                                                $q = $this->db->get('companies');
       if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $customers[]=$row->id;
        }
    }
    }else{
       // die(print_r($data["products"]));    
        $customers=$data["customers"];
    }
    
    //dates
    $startyear=substr($data["period"],-4);
    $currentmonthyear=$data["period"];
 
   $lastyear=$startyear-1;
   
  $thisyeardates="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
  $thisyears=  explode(",", $thisyeardates);
  $index=array_search($currentmonthyear,$thisyears);
 // die(print_r($thisyears));

  for($i=0;$i<=$index;$i++) {
   
    $newdates[]=$thisyears[$i];
  
  }
 
  //$newyeardates=  rtrim($newyeardates);
  
  $dates["allyears"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
  $alldates=explode(",",$dates["allyears"]);
    $alldates=  array_merge($alldates, $newdates);
    
    foreach($customers as $customerid){
                $company=$this->companies_model->getCompanyByID($customerid);  
         
    foreach ($data["countrys"] as $countryid) {
        
        foreach ($products as $prid) {
             $currency=$this->settings_model->getCurrencyByID($countryid);  
           $graph["country_name"]=$currency->country;
           $graph["customer_name"]=$company->name;
       $prdetail=$this->products_model->getProductByID($prid);
       $productname=str_replace(",","",$prdetail->name);
       $graph["name"]=  $productname;
       $cat=$this->products_model->getProductDetails($prid);
       $graph["brand"]=$cat->category_name;
       
       $filters=array("product_id"=>$prid,"country_name"=>$currency->country,"customer"=>$customerid);
       foreach ($alldates as $dateperiod){
           $stock=$this->sales_model->getClosingSaleDit($filters,$dateperiod,"SSO");
       $graph["value"][$currency->country][$company->name][$productname][$cat->category_name][$dateperiod]= $stock["value"]; 
     $graph["qty"][$currency->country][$company->name][$productname][$cat->category_name][$dateperiod]= $stock["qty"];// $this->purchases_model->getClosingStockDit($filters,$dateperiod,"SSO");
       }
       $stockss=$this->purchases_model->getClosingStockDit($filters,$currentmonthyear,"SSO");
       $graph["value_stock"][$currency->country][$company->name][$productname][$cat->category_name][$currentmonthyear]=$stockss["value"];
//$graph["value_stock"][$currency->country][$company->name][$productname][$cat->category_name][$currentdatemonth]=$this->purchases_model->getClosingStockDit($filters,$currentdatemonth,"SSO");
               array_push($graphdata,$graph);
               
               
               
   }
    }
    }
   
   
   
   
//   //calculate months based on year selected
   
// 
//  //calculates sales for each month for each brand
//  $countrydata=array();
//$allgraphs=array();
//            //iterate through chosen ones
//  $salesitems=array("Stock","Month_Cvr");
//            
//            foreach ($salesitems as $cat) {
//               
//              
//     foreach ($alldates as $datemonth) {
//         
//        if ($cat=="Stock"){
//              $stock=$this->purchases_model->getClosingStock($data,$datemonth,"SSO");
//             
//            $monthsalestotals[$datemonth]=$stock;    
//         }
//          else if ($cat=="Month_Cvr"){
//              $stockcover=$this->purchases_model->getMonthStockCover($data,$datemonth,"SSO");
//              
//            $monthsalestotals[$datemonth]=$stockcover;    
//         }
//           
//    }
// 
//    
//$countryy["salestotals"]=$monthsalestotals;
//    
//    $countryy["name"]=$cat;
//      
//          
//    array_push($countrydata, $countryy);
//                
//                
//            }
//            
//             foreach ($alldates as $datemonth) {
//                 $month_name = date("F", mktime(0, 0, 0, substr($datemonth,0,2), 10));
//         $graph["Date"]=substr($month_name,0,3)."-".substr($datemonth,-4);
//        
//             
//         
//            
//            
//              $stock=$this->purchases_model->getClosingStock($data,$datemonth,"SSO");
//              $graph["Stock"]=$stock;
//             
//         
//              $stockcover=$this->purchases_model->getMonthStockCover($data,$datemonth,"SSO");
//              $graph["Month_Cvr"]=$stockcover;
//         
//       array_push($allgraphs,$graph);     
//    }
//            
   
            
    // die(print_r($graphdata));
        
        //end month sales totals
    
  $this->data["countries"]=$graphdata;
 
  $this->data["alldates"]=$alldates;
   $this->data["yearonly"]=$startyear;
    $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['filters']=$filters;
        $this->data["monthyear"]=$currentmonthyear;
         $month_name = date("F", mktime(0, 0, 0, substr($currentmonthyear,0,2), 10)); 
       $this->data["monthyearnames"]=$month_name."-".$startyear;
        $bc = array(array('link' => '#', 'page' => lang('Distributor_SIT')));
        $meta = array('page_title' => lang('Distributor_SIT'), 'bc' => $bc);
        $this->page_construct('distributorsit', $meta, $this->data);
        
    }
    function distributorsitqty(){
       $post=$this->input->post();
       $value="";
    $data=array("cluster"=>$post['cluster'],"countrys"=>$post['f_country'],"gbu"=>$post['gbu'],"promotion"=>$post['promotion'],"productcategoryfamily"=>$post['category'],"customer"=>$post["customer"],"period"=>$post["period"],"grossnet"=>$post["gsales"],"distributor"=>$post["distributor"],"price_type"=>$post["price_type"],"products"=>$post["products"]);
    $filters="";
    //die(print_r($_POST));
   // die(print_r($data));
     if(empty($data["period"])){
        $data=Array ("cluster" => Array ( "0" => "EAH" ), "countrys" => Array (0=>3,1=>6,2=>76), "gbu" => "all", "promotion" =>"", "productcategoryfamily" =>"", "customer" =>"","grossnet" => 1 ,"ssocountry"=>"ssocountry", "period" => date("m-Y") );
$_POST["ssocountry"]=$post["ssocountry"]="ssocountry";
$_POST["gsales"]=$post["gsales"]=1;
    }
    //getStockPerCounntryperBrandPerCustomerfordate
 
    
     if(empty($data["productcategoryfamily"])|| in_array("all",$data["productcategoryfamily"])){
           $categories=$this->settings_model->getAllCategories($data); 
            if($data["gbu"] !="all"){
                foreach ($categories as $value) {
                if($value->id && strtolower($value->gbu)==  strtolower($data["gbu"])){
                 $data['productcategoryfamily'][]=$value->id;
                }
            }
                
            }
            else{
            //iterate through all brands
           
            foreach ($categories as $value) {
                if($value->id){
                 $data['productcategoryfamily'][]=$value->id;
                }
            }
            }
     }
     
  //  die(print_r($data['productcategoryfamily']));
    $graphdata=array();
    
    if(empty($data["products"])|| in_array("all",$data["products"])){
        $productsarray=  $this->products_model->getAllProducts();
        if($data["promotion"] !="all" && !empty($data["promotion"])){
        foreach ($productsarray as $value) {
            if($value->promoted==$data["promotion"]){
            $products[]=$value->id;
            }
        }
        } else{
             foreach ($productsarray as $value) {
            
            $products[]=$value->id;
        }
        }
    }else{
       // die(print_r($data["products"]));    
        $products=$data["products"];
    }
    
    if(empty($data["customer"])|| in_array("all",$data["customer"])){
        $this->db->where(array("group_name"=>"customer"));
                                                $q = $this->db->get('companies');
       if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $customers[]=$row->id;
        }
    }
    }else{
       // die(print_r($data["products"]));    
        $customers=$data["customers"];
    }
    
    //dates
    $startyear=substr($data["period"],-4);
    $currentmonthyear=$data["period"];
 
   $lastyear=$startyear-1;
   
  $thisyeardates="01-".$startyear.",02-".$startyear.",03-".$startyear.",04-".$startyear.",05-".$startyear.",06-".$startyear.",07-".$startyear.",08-".$startyear.",09-".$startyear.",10-".$startyear.",11-".$startyear.",12-".$startyear;
  $thisyears=  explode(",", $thisyeardates);
  $index=array_search($currentmonthyear,$thisyears);
 // die(print_r($thisyears));

  for($i=0;$i<=$index;$i++) {
   
    $newdates[]=$thisyears[$i];
  
  }
 
  //$newyeardates=  rtrim($newyeardates);
  
  $dates["allyears"]="01-".$lastyear.",02-".$lastyear.",03-".$lastyear.",04-".$lastyear.",05-".$lastyear.",06-".$lastyear.",07-".$lastyear.",08-".$lastyear.",09-".$lastyear.",10-".$lastyear.",11-".$lastyear.",12-".$lastyear;    
  $alldates=explode(",",$dates["allyears"]);
    $alldates=  array_merge($alldates, $newdates);
     
    foreach($customers as $customerid){
                $company=$this->companies_model->getCompanyByID($customerid);  
         
    foreach ($data["countrys"] as $countryid) {
        
        foreach ($products as $prid) {
             $currency=$this->settings_model->getCurrencyByID($countryid);  
           $graph["country_name"]=$currency->country;
           $graph["customer_name"]=$company->name;
       $prdetail=$this->products_model->getProductByID($prid);
       $productname=str_replace(",","",$prdetail->name);
       $graph["name"]=  $productname;
       $cat=$this->products_model->getProductDetails($prid);
       $graph["brand"]=$cat->category_name;
       
       $filters=array("product_id"=>$prid,"country_name"=>$currency->country,"customer"=>$customerid);
       foreach ($alldates as $dateperiod){
           $stock=$this->purchases_model->getClosingStockDit($filters,$dateperiod,"SSO");
       $graph["value"][$currency->country][$company->name][$productname][$cat->category_name][$dateperiod]= $stock["value"]; 
     $graph["qty"][$currency->country][$company->name][$productname][$cat->category_name][$dateperiod]= $stock["qty"];// $this->purchases_model->getClosingStockDit($filters,$dateperiod,"SSO");
       }
 $stockss=$this->purchases_model->getClosingStockDit($filters,$currentmonthyear,"SSO");
      $graph["value_stock"][$currency->country][$company->name][$productname][$cat->category_name][$currentmonthyear]=$stockss["value"];
               array_push($graphdata,$graph);
               
               
               
   }
    }
    }
   
   
   

    
  $this->data["countries"]=$graphdata;
 
  $this->data["alldates"]=$alldates;
   $this->data["yearonly"]=$startyear;
   $month_name = date("F", mktime(0, 0, 0, substr($currentmonthyear,0,2), 10)); 
       $this->data["monthyearnames"]=$month_name."-".$startyear;
    $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['filters']=$filters;
        $this->data["monthyear"]=$currentmonthyear;
       
        $bc = array(array('link' => '#', 'page' => lang('Distributor_SIT')));
        $meta = array('page_title' => lang('Distributor_SIT'), 'bc' => $bc);
        $this->page_construct('distributorsitqty', $meta, $this->data);
        
    }
    
      function msranalysis(){
       $post=$this->input->post();
      $this->db->cache_off();
       
$periods=  $this->input->post("period");
$dates="";
foreach ($periods as $value) {
    $correcteddate= substr($value,-4)."-".substr($value,0,2)."-01";//month
    $dates.=',"'.$correcteddate.'"';
}
  $newdates=ltrim($dates,",");
  
   if(!is_array($periods)){
       $newdates='"'.date("Y")."-".date("m")."-01".'"';
       // $this->session->set_flashdata('error', $this->lang->line("please_select_period_first"));
      // redirect($_SERVER["HTTP_REFERER"]);
   }
 
 //die(print_r($newdates));
 
  
    $teams=$this->site->getAllTeams();
    foreach ($teams as $team){
      $alignments=$this->site->getTeamAlignments($team->id);
    // print_r($alignments);
        foreach ($alignments as $value) {
            
        $align["team_name"]=$team->name;
        $align["sf_name"]=$value->sf_alignment;
        $align["sf_id"]=$value->sf_alignment_id;
        $align["alignment_products_customers"]=$this->site->getAlignmentCustomersAndProducts($value->sf_alignment_id);
      //$align["budget"]= 
       $graphdata[]=$align;
        }
       
    }
    

    
  $this->data["alldata"]=$graphdata;
 
  $this->data["newdates"]=$newdates;
   $this->data["yearonly"]=$startyear;
   $month_name = date("F", mktime(0, 0, 0, substr($currentmonthyear,0,2), 10)); 
       $this->data["monthyearnames"]=$month_name."-".$startyear;
    $this->data['currencies']=  $this->settings_model->getAllCurrencies();
        $this->data['clusters']=  $this->cluster_model->getClusters();
        $this->data['filters']=$filters;
        $this->data["monthyear"]=$currentmonthyear;
       
        $bc = array(array('link' => '#', 'page' => lang('MSR Analysis')));
        $meta = array('page_title' => lang('MSR Analysis'), 'bc' => $bc);
        $this->page_construct('msr', $meta, $this->data);
        
    }
    
    
}
