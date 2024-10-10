
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css">

</head>
<body>
   
<div class="container">
    <input type="text" name="date" class="form-control datepicker" autocomplete="off">
</div>
   
</body>
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
    // This can come from php
    // var disableDates = [<?php //echo $your_variables_from_php_mysql_printed_in_the_format_below; ?>]
    var disableDates = ["7-7-2020", "14-7-2020", "15-7-2020","27-7-2020"];
      
    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy',
        beforeShowDay: function(date){
            dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
            if(disableDates.indexOf(dmy) != -1){
                return false;
            }
            else{
                return true;
            }
        }
    });
</script>
</html>
