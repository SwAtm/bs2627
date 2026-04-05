<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bill_handler {

    protected $CI;

    public function __construct() {
        // Assign the CodeIgniter super-object to $this->CI
        // This allows you to use $this->CI->db, $this->CI->session, etc.
        $this->CI =& get_instance();
        $this->CI->load->model('Party_model');
		$this->CI->load->model('Trns_summary_model');
		$this->CI->load->model('Trns_details_model');
		$this->CI->load->library('session');
		$this->CI->load->helper('pdf_helper');
		//$this->output->enable_profiler(TRUE);
		$this->CI->load->library('Qrcodeg');
	}


	public function print_bill($id, $mode='I'){
		//$id = $this->uri->segment(3);
		tfpdf();
		$data['summary']= $this->CI->Trns_summary_model->get_details_by_id($id);
		$data['party'] = $this->CI->Party_model->get_details($data['summary']['party_id']);
		//$data['details'] = $this->Trns_details_model->get_details($data['summary']['id']);
		$data['location'] = $this->CI->session->loc_name;
		$gst = 0;
		$data['taxamt'] = 0;
		$data['notaxamt'] = 0;
		$data['totamount']= 0;
		$data['totquantity']=0;
		$details = $this->CI->Trns_details_model->get_details($data['summary']['id']);
		//sum quantity in details array
		$newdetails=array();
		$found='n';
		foreach ($details as $k):
			foreach ($newdetails as $key=>$d):
				if($k['item_id']==$d['item_id'] and $k['hsn']==$d['hsn'] and $k['rate']==$d['rate'] and $k['discount']==$d['discount'] and $k['cash_disc']==$d['cash_disc']):
					$newdetails[$key]['quantity']+=$k['quantity'];
					$newdetails[$key]['amount']+=$k['amount'];
				$found='y';
				endif;
			endforeach;
		if ('n'==$found):
		$newdetails[]=$k;
		endif;
		$found='n';
		endforeach;			
		$data['details']=$newdetails;	
		
		//This is not reqd now. If RCM comes into effect, then this will be reqd since then in details file gst rate would have been recorded to work out RCM.
		/*
		if (strtoupper($data['summary']['party_status'])!='REGD' AND strtoupper($data['summary']['tran_type_name'])=='PURCHASE'):
			foreach ($data['details'] as $det=>$v):
				$data['details'][$det]['gst_rate']=0;
				//print_r($det);
				//echo "<br>";
			endforeach;
		endif;
		*/
				
		foreach ($data['details'] as $det):
			if ($det['gst_rate']>0):
			$data['taxamt']+=$det['amount']/(100+$det['gst_rate'])*100;
			$gst+=$det['amount']/(100+$det['gst_rate'])*$det['gst_rate'];
			else:
			$data['notaxamt']+=$det['amount'];
			endif;
		$data['totamount']+=$det['amount'];
		$data['totquantity']+=$det['quantity'];
		endforeach;
		if (strtoupper($data['summary']['party_state_io'])=='I'):
			$data['cgst'] = $data['sgst'] = $gst/2;
			$data['igst'] = 0;
		else:
			$data['igst'] = $gst;
			$data['cgst'] = $data['sgst'] = 0;
		endif;
		$data['totamount']+=$data['summary']['expenses'];
		//generate QR Code for UPI Sales
		if ($data['summary']['payment_mode_name'] == "UPI" and $data['summary']['tran_type_name'] == "Sales"):
		$text = "upi://pay?pa=".   			 // payment method.
                //"gpay-11192753290@okbizaxis".          // VPA number.
                //"373901010035580@UBIN0537390.ifsc.npci".          // VPA number.
                "ramak83109@barodampay".          // VPA number.
                "&am=".number_format($data['totamount'],2,".",",").       // this param is for fixed amount (non editable).
                "&pn=Ramakrishna%20Mission Ashrama, Belgaum".      // to showing your name in app.
                "&cu=INR".                  // Currency code.
                "&mode=02";                 // mode O2 for Secure QR Code.
                //"&trxnID=".$data['summary']['payment_mode_name'].' - '.$data['summary']['tran_type_name']. ' - '.$data['summary']['no'];
                //"&orgid=189999" +            //If the transaction is initiated by any PSP app then the respective orgID needs to be passed.
                //"&sign=MEYCIQC8bLDdRbDhpsPAt9wR1a0pcEssDaV".   // Base 64 encoded Digital signature needs to be passed in this tag
                //"Q7lugo8mfJhDk6wIhANZkbXOWWR2lhJOH2Qs/OQRaRFD2oBuPCGtrMaVFR23t"
		$file = SAVEPATH.'/qrc.png';
		$ecc = 'L';
		$pixel_Size = 40;
		$frame_Size = 0;
		QRcode::png($text, $file, $ecc, $pixel_Size, $frame_Size);
		endif;
		
		if ((count($data['details'])>12) OR ($data['summary']['payment_mode_name']=='Credit' and $data['summary']['tran_type_name'] == 'Sales')):
		$pdf = new tFPDF('P', 'mm', array(210,296));
		$y = 266;
		//$noofpages = ceil(count($details)/28);
		else:
		$pdf = new tFPDF('L', 'mm', array(210,148));
		$y = 118;
		endif;
		$data['pdf']=$pdf;
		$data['y']=$y;
		$data['mode']=$mode;		
		$this->CI->load->view('reports/print_bill',$data);
		
		$filename=$data['summary']['payment_mode_name'].'_'.$data['summary']['tran_type_name']. '_'.$data['summary']['no'].".pdf";
		$fullpath = SAVEPATH . $filename;

    if ($mode == 'F') {
        $pdf->Output($fullpath, 'F');
        $datatoreturn=array('fullpath'=>$fullpath, 'amount'=>$data['totamount'], 'date'=>$data['summary']['date'], 'bill_no'=>$filename, 'mobile'=>$data['summary']['mobile']);
        return $datatoreturn;
    } else {
        $pdf->Output($filename, 'I');
        exit;
    }
	
}		
}
?>
