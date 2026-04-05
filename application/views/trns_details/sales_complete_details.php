<!Doctype HTML>
	<!DOCTYPE html>
	<html>
	<head>
		<title></title>
<script>
	window.onload = function() {
	let partyhandle = document.querySelector('#party');
	partyhandle.focus();
}
</script>
<style>
	#tb{
		width: 100%;
		border: 1px solid black;	
	}
	#tb tr, #tb td{
		border: 1px solid black;
	}
</style>

	</head>
	<body>
<table id = tb >
	<caption>Please complete the transaction</caption>
	<tr><th>Party</th><th style = "width: 15%">Transaction Type</th><th style = "width: 15%">Expenses</th><th style = "width: 10%">Mobile</th><th style = "width: 15%">Remarks</th></tr>

    <tr>
        <td>
            <!-- Use PHP only for the dynamic action URL -->
            <form method="POST" action="<?php echo site_url('trns_details/sales_complete_details'); ?>">
                <select name="party" id="party" required>
                    <option value="">Select Party</option>
                    <?php foreach ($party as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($p['id'] == 122) ? 'selected' : ''; ?>>
                            <?php echo $p['name']; ?> -- <?php echo $p['city']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
        </td>
        <td>
            <select name="series" id="series" required>
                <option value="">Select Transaction Type</option>
                <?php foreach ($series as $s): ?>
                    <option value="<?php echo $s['id']; ?>">
                        <?php echo $s['payment_mode_name']; ?> -- <?php echo $s['tran_type_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" name="expenses" placeholder="Expenses">
        </td>
        <td>
            <!-- The 'pattern' attribute ensures exactly 10 digits are entered before submission -->
            <input type="tel" name="mobile" maxlength="10" pattern="[0-9]{10}" placeholder="Mobile">
        </td>
        <td>
            <input type="text" name="remark" placeholder="Remark">
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <input type="submit" name="finalize" value="Finalize">
        </td>
        <td align="center" colspan="3">
            <input type="submit" name="cancel" id="cancel" formnovalidate="formnovalidate" value="Cancel Bill">
        </td>
    </tr>
    </form> <!-- Form closed outside the TD to encompass the submit buttons -->
</table>


	</body>
	</html>
