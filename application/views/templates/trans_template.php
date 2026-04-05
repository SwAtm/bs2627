<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
 
<?php 
foreach($css_files as $file): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
 
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
 
    <script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
<style type='text/css'>
body
{
    font-family: Arial;
    font-size: 14px;
}
a {
    color: blue;
    text-decoration: none;
    font-size: 14px;
}
a:hover
{
    text-decoration: underline;
}
</style>
</head>
<body>
	
<?php if(!empty($message)): ?>
    <div style="padding: 15px; background-color: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; border-radius: 4px; margin-bottom: 20px;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>


    <div style='height:20px;'></div>  
    <div>
    <?php echo $extra?>
    </div>





    <div>
<?php echo $output; ?>
 
    </div>
    

<!--</body>
</html>
--> 
