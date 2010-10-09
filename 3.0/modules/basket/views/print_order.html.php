<html>

<head>
<title>Print Order</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script language="javascript">
$(document).ready(function(){
 window.print();
 window.close();
});
</script>
</head>

<body>
 <tt>
 <?= $order ?>
 </tt>
</body>

</html>