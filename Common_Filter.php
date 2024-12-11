<?php 




error_reporting(-1);
Class Common_Filter{
  public $conn;

  function __construct($conn) {
      $this->conn = $conn;

    }

  private function get_Sql_Result($Sql_Dets){
    $result=array();
    while($value=sqlsrv_fetch_array($Sql_Dets)){
      $result[]=$value;
    }
    return $result;
  }


 public function Purchase_report($User_Input=array())
  {
    // echo "<pre>";print_r($User_Input['user_input']['zone']);exit;

    $Offset=@$User_Input['start'] !='' ? @$User_Input['start'] : 0;
    $Length=@$User_Input['length'];
  $Emp_Id=isset($_SESSION['EmpID']) && ($_SESSION['EmpID'] !='Admin' || $_SESSION['EmpID'] !='SuperAdmin') ? $_SESSION['EmpID'] : '';
    $Dcode=$_SESSION['Dcode'];



    $sno=$Offset+1;
    $recordsTotal=0;
    $resultarr=array();
    $i=0;
    $Sql="SELECT TOTALROW = COUNT(*) OVER(),Tb_Request.Id,Tb_Request.Request_ID,Tb_Request.Request_Type,CONVERT(VARCHAR(10), Tb_Request.Time_Log, 103)Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,Price,Tb_Vendor_Selection.discount_amount,Tb_Vendor_Selection.GST_Component,Tb_Vendor_Selection.total_amount,(Case When Tb_Request.status='Requested' Then 'Request Created' When Tb_Request.status='Quoted' Then 'Quotaion Created' When Tb_Request.status='Recommended' Then 'Recommended' When Tb_Request.status='Approved' Then 'Approved' When Tb_Request.status='Approver_Reject' Then 'Approver Rejected' else '' end)as Approval_status,HR_Master_Table.Employee_Name  FROM Tb_Request LEFT JOIN Tb_Vendor_Selection On Tb_Vendor_Selection.Request_Id=Tb_Request.Request_ID LEFT JOIN Tb_Vendor_Quantity On Tb_Vendor_Quantity.Request_Id=Tb_Request.Request_ID LEFT JOIN Tb_Approver On Tb_Approver.Request_Id=Tb_Request.Request_ID LEFT JOIN MaterialMaster On MaterialMaster.ItemCode=Tb_Vendor_Quantity.Meterial_Name LEFT JOIN HR_Master_Table On HR_Master_Table.Employee_Code=Tb_Request.EMP_ID WHERE 1=1 AND (HR_Master_Table.L1_Manager_Code='".$_SESSION['EmpID']."' OR Tb_Request.Approver='".$_SESSION['EmpID']."') GROUP BY Tb_Request.Request_ID,Tb_Request.Request_Type,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Request.Id,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,Price,Tb_Vendor_Selection.GST_Component,Tb_Vendor_Selection.total_amount,Tb_Vendor_Selection.discount_amount,HR_Master_Table.Employee_Name  ORDER BY Tb_Request.Id DESC OFFSET $Offset ROWS FETCH NEXT $Length ROWS ONLY";


     



    $Sql_Connection =sqlsrv_query($this->conn,$Sql);
    while($Sql_Result = sqlsrv_fetch_array($Sql_Connection))
    {
      $recordsTotal = @$Sql_Result['TOTALROW'];
      $resarr = array();
      $resarr[] = $sno++;
     $resarr[] = @$Sql_Result['Request_ID'];
     $resarr[] = @$Sql_Result['Time_Log'];
     $resarr[] = @$Sql_Result['Employee_Name'];
     $resarr[] = @$Sql_Result['Request_Type'];
     $resarr[] = @$Sql_Result['Request_Category'];
     $resarr[] = @$Sql_Result['Department'];
     $resarr[] = @$Sql_Result['Vendor_SAP'];
     $resarr[] = @$Sql_Result['Vendor_Name'];
     $resarr[] = @$Sql_Result['Vendor_City'];
     $resarr[] = @$Sql_Result['ItemCode'];
     $resarr[] = @$Sql_Result['ItemDescription'];
     $resarr[] = @$Sql_Result['Price'];
     $resarr[] = @$Sql_Result['discount_amount'];
     $resarr[] = @$Sql_Result['GST_Component'];
     $resarr[] = @$Sql_Result['total_amount'];
     $resarr[] = @$Sql_Result['Approval_status'];
     //$resarr[] = "";
      
   
  
     
       
      $resultarr[] = $resarr;
      $i++;
    }
    $res=array();
    if(isset($User_Input['draw']))
    {
      $res['draw'] = @$User_Input['draw'];  
    }else
    {
      $res['draw'] = 1; 
    }
    $res['recordsFiltered'] = @$recordsTotal;
    $res['recordsTotal'] = @$recordsTotal;
    $res['data'] = @$resultarr;
    $res['sql'] = @$Sql;
    $result = $res;
    return $result;
  }

public function get_po_number($request_id)
{
    $api_url = 'http://192.168.162.213:8081/PR_PO_CREATE/PRD/ZIN_RFC_GET_PR_PO_NUMBER.php?PO_REQUEST_ID='.$request_id;

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $api_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    return json_decode($response,true);

}


public function Purchase_report_dev($User_Input=array())
  {
    // echo "<pre>";print_r($User_Input['user_input']['zone']);exit;


    $Offset=@$User_Input['start'] !='' ? @$User_Input['start'] : 0;
    $Length= ($User_Input['length'] == 'All') ? 1000000 : @$User_Input['length'];
  $Emp_Id=isset($_SESSION['EmpID']) && ($_SESSION['EmpID'] !='Admin' || $_SESSION['EmpID'] !='SuperAdmin') ? $_SESSION['EmpID'] : '';
    $Dcode=$_SESSION['Dcode'];



    $sno=$Offset+1;
    $recordsTotal=0;
    $resultarr=array();
    $i=0;
    $Sql="SELECT TOTALROW = COUNT(*) OVER(),Tb_Request.Id,Tb_Request.Request_ID,Tb_Request.Request_Type,CONVERT(VARCHAR(10),Tb_Request.Time_Log, 103)Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Approver.Time_Log as approved_Time_Log,
      (Case When Tb_Request.status='Requested' Then 'Waiting for quotation'
      When Tb_Request.status='Added' Then 'Waiting for recommendation' 
      When Tb_Request.status='Recommended' Then 'Waiting for approval' 
      When Tb_Request.status='Approved' Then 'Approved' 
      When Tb_Request.status='Approver_Reject' Then 'Approver Rejected'
      When Tb_Request.status='Recommender_Rejected' Then 'Recommender Rejected' 
      When Tb_Request.status='Approver2_Reject' Then 'Approver2 Rejected' 
      When Tb_Request.status='Waiting_for_approval2' Then 'Waiting for approval2' 
      else Tb_Request.status end) as Approval_status,Tb_Request.Plant,

      (Case When Tb_Request.status='Requested' Then '-' 
      When Tb_Request.status='Added' Then TRIM(Tb_Vendor_Selection.Vendor_SAP)+'-'+Tb_Vendor_Selection.Vendor_Name
      When Tb_Request.status='Recommended' Then TRIM(Tb_Recommender.Vendor_SAP)+'-'+Tb_Recommender.Vendor_Name
      When (Tb_Request.status='Approved' AND Tb_Master_Emp.Approver_2 IS NULL) Then TRIM(Tb_Approver.Vendor_SAP)+'-'+Tb_Approver.Vendor_Name 
      When (Tb_Request.status='Approved' AND Tb_Master_Emp.Approver_2 IS NOT NULL) Then TRIM(approver2_tbl.Vendor_SAP)+'-'+approver2_tbl.Vendor_Name
      When Tb_Request.status='Approver_Reject' Then TRIM(Tb_Recommender.Vendor_SAP)+'-'+Tb_Recommender.Vendor_Name  
      When Tb_Request.status='Recommender_Rejected'  Then TRIM(Tb_Vendor_Selection.Vendor_SAP)+'-'+Tb_Vendor_Selection.Vendor_Name   
      When Tb_Request.status='Approver2_Reject' Then TRIM(approver2_tbl.Vendor_SAP)+'-'+approver2_tbl.Vendor_Name  
      When Tb_Request.status='Waiting_for_approval2' Then TRIM(approver2_tbl.Vendor_SAP)+'-'+approver2_tbl.Vendor_Name  
      else Tb_Request.status end) as vendor_name,

      (Case When Tb_Request.status='Requested' Then Tb_Request.Requested_to+'-'+purchaser_name_tbl.Employee_Name
      When Tb_Request.status='Added' Then Tb_Master_Emp.Recommender+'-'+recommender_name_tbl.Employee_Name 
      When Tb_Request.status='Recommended' Then Tb_Master_Emp.Approver+'-'+approver_name_tbl.Employee_Name
      When Tb_Request.status='Waiting_for_approval2' Then Tb_Master_Emp.Approver_2+'-'+approver2_name_tbl.Employee_Name
      When (Tb_Request.status='Approved' AND Tb_Master_Emp.Approver_2 IS NULL) Then Tb_Master_Emp.Approver+'-'+approver_name_tbl.Employee_Name
      When (Tb_Request.status='Approved' AND Tb_Master_Emp.Approver_2 IS NOT NULL) Then Tb_Master_Emp.Approver_2+'-'+approver2_name_tbl.Employee_Name 
      When Tb_Request.status='Approver_Reject' Then Tb_Master_Emp.Approver+'-'+approver_name_tbl.Employee_Name 
      When Tb_Request.status='Recommender_Rejected' Then Tb_Master_Emp.Recommender+'-'+recommender_name_tbl.Employee_Name 
      When Tb_Request.status='Approver2_Reject' Then Tb_Master_Emp.Approver_2+'-'+approver2_name_tbl.Employee_Name 
      else Tb_Request.status end) as status_emp_code

      FROM Tb_Request 
      LEFT JOIN Tb_Vendor_Selection On Tb_Vendor_Selection.Request_Id=Tb_Request.Request_ID and Tb_Vendor_Selection.Requester_Selection = '1'
      LEFT JOIN Tb_Vendor_Quantity On Tb_Vendor_Quantity.Request_Id=Tb_Request.Request_ID 
      LEFT JOIN Tb_Recommender On Tb_Recommender.Request_Id=Tb_Request.Request_ID and Tb_Recommender.Recommender_Selection = '1'
      LEFT JOIN Tb_Approver On Tb_Approver.Request_Id=Tb_Request.Request_ID  and Tb_Approver.Approver_Selection = '1'
      LEFT JOIN Tb_Approver as approver2_tbl On approver2_tbl.Request_Id=Tb_Request.Request_ID and approver2_tbl.Approver_Selection = '1'
      LEFT JOIN MaterialMaster On MaterialMaster.ItemCode=Tb_Vendor_Quantity.Meterial_Name 
      LEFT JOIN HR_Master_Table On HR_Master_Table.Employee_Code=Tb_Request.EMP_ID 
      LEFT JOIN Tb_Master_Emp On Tb_Master_Emp.id = Tb_Request.approval_mapping_id
      LEFT JOIN HR_Master_Table as purchaser_name_tbl On purchaser_name_tbl.Employee_Code=TRIM(Tb_Request.Requested_to)
      LEFT JOIN HR_Master_Table as recommender_name_tbl On recommender_name_tbl.Employee_Code=TRIM(Tb_Master_Emp.Recommender)
      LEFT JOIN HR_Master_Table as approver_name_tbl On approver_name_tbl.Employee_Code=TRIM(Tb_Master_Emp.Approver)
      LEFT JOIN HR_Master_Table as approver2_name_tbl On approver2_name_tbl.Employee_Code=TRIM(Tb_Master_Emp.Approver_2) 
      WHERE 1=1 ";


if($Emp_Id!='RS6064'){
 $Sql.="AND (HR_Master_Table.L1_Manager_Code='".$_SESSION['EmpID']."' OR Tb_Request.Approver='".$_SESSION['EmpID']."' OR Tb_Request.EMP_ID='".$_SESSION['EmpID']."' OR Tb_Request.Requested_to='".$_SESSION['EmpID']."') 
      GROUP BY Tb_Request.Request_ID,Tb_Request.Request_Type,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Request.Id,Tb_Request.Plant,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_SAP,Tb_Approver.Vendor_Name,Tb_Approver.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_SAP,Tb_Master_Emp.Purchaser ,Tb_Master_Emp.Recommender,Tb_Master_Emp.Approver,Tb_Master_Emp.Approver_2,purchaser_name_tbl.Employee_Name
    ,recommender_name_tbl.Employee_Name,approver_name_tbl.Employee_Name,approver2_name_tbl.Employee_Name,approver2_tbl.Vendor_SAP,approver2_tbl.Vendor_Name,Tb_Request.Requested_to,Tb_Approver.Time_Log  ORDER BY Tb_Request.Id DESC OFFSET $Offset ROWS FETCH NEXT $Length ROWS ONLY";

}else{


   $Sql.="AND Tb_Request.EMP_ID!='RS7361' GROUP BY Tb_Request.Request_ID,Tb_Request.Request_Type,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Request.Id,Tb_Request.Plant,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_SAP,Tb_Approver.Vendor_Name,Tb_Approver.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_SAP,Tb_Master_Emp.Purchaser ,Tb_Master_Emp.Recommender,Tb_Master_Emp.Approver,Tb_Master_Emp.Approver_2,purchaser_name_tbl.Employee_Name
    ,recommender_name_tbl.Employee_Name,approver_name_tbl.Employee_Name,approver2_name_tbl.Employee_Name,approver2_tbl.Vendor_SAP,approver2_tbl.Vendor_Name,Tb_Request.Requested_to,Tb_Approver.Time_Log  ORDER BY Tb_Request.Id DESC OFFSET $Offset ROWS FETCH NEXT $Length ROWS ONLY";
}

     

    // echo $Sql;exit;

    $Sql_Connection =sqlsrv_query($this->conn,$Sql);
    while($Sql_Result = sqlsrv_fetch_array($Sql_Connection))
    {
      $recordsTotal = @$Sql_Result['TOTALROW'];
      $resarr = array();
      $resarr[] = $sno++;
     $resarr[] = @$Sql_Result['Request_ID'];
     $resarr[] = @$Sql_Result['Time_Log'];
     $resarr[] = @$Sql_Result['Request_Type'];
     $resarr[] = @$Sql_Result['vendor_name'];
     $resarr[] = @$Sql_Result['Department'];
     $resarr[] = @$Sql_Result['Plant'];

     if($User_Input['function'] == 'list') {
       if($Sql_Result['Approval_status'] == 'Waiting for quotation') {
            $resarr[] = '<span class="badge badge-soft-primary text-wrap" style="width:102px;line-height: 18px;">'.$Sql_Result['Approval_status'].'-'.$Sql_Result['status_emp_code'].'</span>';      
       } elseif($Sql_Result['Approval_status'] == 'Waiting for recommendation') {
            $resarr[] = '<span class="badge badge-soft-info text-wrap" style="width:102px;line-height: 18px;">'.$Sql_Result['Approval_status'].'-'.$Sql_Result['status_emp_code'].'</span>';      
       } elseif($Sql_Result['Approval_status'] == 'Waiting for approval' || $Sql_Result['Approval_status'] == 'Waiting for approval2') {
            $resarr[] = '<span class="badge badge-soft-warning text-wrap" style="width:102px;line-height: 18px;">'.$Sql_Result['Approval_status'].'-'.$Sql_Result['status_emp_code'].'</span>';      
       } elseif($Sql_Result['Approval_status'] == 'Approved') {
            $resarr[] = '<span class="badge badge-soft-success text-wrap" style="width:102px;line-height: 18px;">'.$Sql_Result['Approval_status'].'-'.$Sql_Result['status_emp_code'].'</span>';      
       } elseif($Sql_Result['Approval_status'] == 'Approver Rejected' || $Sql_Result['Approval_status'] == 'Recommender Rejected') {
            $resarr[] = '<span class="badge badge-soft-danger text-wrap" style="width:102px;line-height: 18px;">'.$Sql_Result['Approval_status'].'-'.$Sql_Result['status_emp_code'].'</span>';      
       } else {
            $resarr[] = '<span class="badge badge-soft-danger text-wrap" style="width:102px;line-height: 18px;">'.$Sql_Result['Approval_status'].'-'.$Sql_Result['status_emp_code'].'</span>';      
       }


       //po number get from the api
       $po_response = $this->get_po_number($Sql_Result['Request_ID']);
       $po_response = (count($po_response['result']) > 0) ? (($po_response['result'][0]['PO_NUMBER'] != '') ? $po_response['result'][0]['PO_NUMBER'] : '-') : '-'; 

       $resarr[]  = $po_response;
       $resarr[]  = ($Sql_Result['approved_Time_Log'] != '') ? date('d/m/Y h:i:s A',strtotime($Sql_Result['approved_Time_Log'])) : '-';


       // $resarr[] = @$Sql_Result['Approval_status'];
       if($Sql_Result['Approval_status'] == 'Request Created' || $Sql_Result['Approval_status'] == 'Waiting for quotation') {
          $resarr[] = '<a href="view_purchasevendor_request.php?purchase_id='.$Sql_Result['Request_ID'].'" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View"><i class="mdi mdi-eye"></i></a>';
       } else {
          $resarr[] = '<a href="view_quotation_request.php?id='.$Sql_Result['Request_ID'].'" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View"><i class="mdi mdi-eye"></i></a>';
       }
     } elseif ($User_Input['function'] == 'export') {
        $resarr[] = $Sql_Result['Approval_status'].'-'.$Sql_Result['status_emp_code'];

        //po number get from the api
       $po_response = $this->get_po_number($Sql_Result['Request_ID']);
       $po_response = (count($po_response['result']) > 0) ? (($po_response['result'][0]['PO_NUMBER'] != '') ? $po_response['result'][0]['PO_NUMBER'] : '-') : '-'; 

       $resarr[]  = $po_response;
       $resarr[]  = ($Sql_Result['approved_Time_Log'] != '') ? date('d/m/Y h:i:s A',strtotime($Sql_Result['approved_Time_Log'])) : '-';
     }



         
       
      $resultarr[] = $resarr;
      $i++;
    }
    $res=array();
    if(isset($User_Input['draw']))
    {
      $res['draw'] = @$User_Input['draw'];  
    }else
    {
      $res['draw'] = 1; 
    }
    $res['recordsFiltered'] = @$recordsTotal;
    $res['recordsTotal'] = @$recordsTotal;
    $res['data'] = @$resultarr;
    $res['sql'] = @$Sql;
    $result = $res;
    return $result;
  }




  
   

}?>