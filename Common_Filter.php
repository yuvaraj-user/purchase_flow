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


  
   

}?>