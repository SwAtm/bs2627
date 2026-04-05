<!DOCTYPE html>
<html>
<head>
    <title>Update Transaction Mobile</title>
    <style>
        .container { width: 500px; margin: 50px auto; font-family: sans-serif; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        .readonly-box { background: #f9f9f9; padding: 15px; border-left: 5px solid #2196F3; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: red; font-size: 0.9em; }
        .btn-save { background: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 4px; }
        .btn-cancel { color: #666; text-decoration: none; margin-left: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Mobile Number</h2>

    <div class="readonly-box">
        <p><strong>Bill No:</strong> <?php echo $record['series'].' - '.$record['no']; ?></p>
        <p><strong>Date:</strong> <?php echo date('d-M-Y', strtotime($record['date'])); ?></p>
        
    </div>

    <?php echo form_open('trns_summary/addeditmobile/' . $record['id']); ?>
        <?php echo form_hidden('id', $record['id']); ?>
        <div class="form-group">
            <label for="mobile">INDIAN Mobile Number: 10 digits, no spaces</label>
            <input type="text" name="mobile" id="mobile" maxlength="10" pattern="\d{10}"
                   value="<?php echo set_value('mobile', $record['mobile']); ?>" 
                   placeholder="Enter 10-digit number" autofocus>
            <div class="error"><?php echo form_error('mobile'); ?></div>
        </div>

        <button type="submit" class="btn-save">Update Record</button>
        <a href="<?php echo site_url('trns_summary/summary'); ?>" class="btn-cancel">Cancel</a>

    <?php echo form_close(); ?>
</div>

</body>
</html>
