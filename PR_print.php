<?php
include '../auto_load.php';  

$request_id = $_POST['request_id'];

// $request_id = 'PV000221';

$sql = "SELECT * from Tb_Request 
INNER JOIN Tb_Request_Items ON Tb_Request_Items.Request_ID = Tb_Request.Request_ID  
LEFT JOIN HR_Master_Table ON HR_Master_Table.Employee_Code = Tb_Request.EMP_ID
LEFT JOIN MaterialMaster ON MaterialMaster.ItemCode = Tb_Request_Items.Item_Code and MaterialMaster.Plant = Tb_Request.Plant 
and MaterialMaster.StorageLocation = Tb_Request.Storage_Location
LEFT JOIN SERVICE_MATERIAL_MASTER on SERVICE_MATERIAL_MASTER.ASNUM = Tb_Request_Items.Item_Code
WHERE Tb_Request.Request_ID = '".$request_id."'";  

$sql_exec = sqlsrv_query($conn,$sql);
$result = array();
while($sql_result = sqlsrv_fetch_array($sql_exec,SQLSRV_FETCH_ASSOC)) {
    $result[] = $sql_result; 
} 

// echo "<pre>";print_r($result[0]);exit;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"/>
    <style type="text/css">
        .br-bottom {
            border-bottom: 1px solid black !important;
        }

        .br-top {
            border-top: 1px solid black !important; 
        }
        .f-14 {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 col-lg-2 col-xl-2 col-xxl-2 col-2">
                <img src="logo.png" alt="" height="70">
            </div>

            <div class="col-md-9 col-lg-9 col-xl-9 col-xxl-9 col-9">
                <div class="head text-center"> 
                    <h5 class="fw-bold">Rasi Seeds Private Limited,</h5>
                    <p class="mb-0 f-14">Unit II,Attur-B,3/113,CUDDALORE MAIN ROAD,THULUKKANUR Attur - 636141,</p>
                    <p class="f-14">Tamil Nadu</p>

                    <p class="fw-bold">Purchase requistion note/Indent</p>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 col-6 p-0">
                <div class="d-flex">
                    <p class="fw-bold me-2 col-4">Vendor Code</p>
                    <p class="fw-bold me-2">:</p>
                    <p class="col-4"></p>
                </div>
                <div class="d-flex">
                    <p class="fw-bold me-2 col-4">Department</p>
                    <p class="fw-bold me-2">:</p>
                    <p class="col-6"><?php echo $result[0]['Department']; ?></p>
                </div>
            </div>

            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 col-6 p-0">
                <div class="d-flex">
                    <p class="fw-bold me-2 col-4">Vendor Name</p>
                    <p class="fw-bold me-2">:</p>
                    <p class="col-4"></p>
                </div>
                <div class="d-flex">
                    <p class="fw-bold me-2 col-4">PR No</p>
                    <p class="fw-bold me-2">:</p>                    
                    <p class="col-4"><?php echo $request_id; ?></p>
                </div>
            </div>
 
        </div>

        <div class="row">
            <table class="br-bottom" cellpadding="3">
                <thead>
                    <tr class="br-bottom br-top">
                        <th>S.No</th>
                        <th>HSN Code</th>
                        <th>Material Description</th>
                        <th>Plant</th>
                        <th>Storage Location</th>
                        <th>Qty</th>
                        <th>UOM</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($result as $key => $value) { ?>
                    <tr>
                        <td><?php echo $key+1 ?></td>
                        <td><?php echo ($value['Request_Type'] == 'Services') ? $value['TAXTARIFFCODE'] : $value['HSN_Code']; ?></td>
                        <td><?php echo $value['Description']; ?></td>
                        <td><?php echo $value['Plant']; ?></td>
                        <td><?php echo $value['Storage_Location'] ?></td>
                        <td><?php echo $value['Quantity'] ?></td>
                        <td><?php echo $value['UOM'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="row mt-1 br-bottom">
                <div class="d-flex p-0">
                    <p class="fw-bold me-2 mt-3">Material to Be Handed Over to: </p>
                    <p class="me-2 mt-3"><?php echo $result[0]['EMP_ID'].' '.$result[0]['Employee_Name']; ?></p>

                </div>
                <div class="d-flex p-0">
                    <p class="fw-bold me-2">Phone No: </p>
                    <p class="me-2"><?php echo $result[0]['Mobile_No']; ?></p>
                </div>
            </div>


            <div class="row mt-1 br-bottom">
                <div class="col-md-5 col-lg-5 col-xl-5 col-xxl-5 col-5 p-0">
                        <p class="fw-bold">Recommended By</p>
                        <p class="fw-bold p-2"></p>
                        <p class="fw-bold">Signature</p>
                </div>
                <div class="col-md-7 col-lg-7 col-xl-7 col-xxl-7 col-7 p-0">
                    <div class="d-flex p-0 justify-content-end"> 
                        <p class="fw-bold me-2 col-4">Entered By</p>
                        <p class="fw-bold me-2">:</p>
                        <p class="col-5"><?php echo $result[0]['Employee_Name'].'('.$result[0]['EMP_ID'].')'; ?></p>
                    </div>
                    <div class="d-flex p-0 justify-content-end">
                        <p class="fw-bold me-2 col-4">Purchase Group</p>
                        <p class="fw-bold me-2">:</p>
                        <p class="col-5"><?php echo $result[0]['MaterialGroup']; ?></p>
                    </div>
                    <div class="d-flex p-0 justify-content-end">
                        <p class="fw-bold me-2 col-4">Created On</p>
                        <p class="fw-bold me-2">:</p>
                        <p class="col-5"><?php echo $result[0]['Time_Log']->format('d-m-Y'); ?></p>
                    </div>                                        
                </div>
            </div>

        </div>

    </div>

</body>
</html>
