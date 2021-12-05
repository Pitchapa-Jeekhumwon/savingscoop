<?php
   $data = $loan_fscoop;
   $data2 = $loan_share;

   $age                = $this->center_function->diff_birthday($data['birthday']); //อายุ
   $monthtext          = $this->center_function->month_arr(); // function แปลงเดือนเป็นตัวอักษร
   $money_loan_amount_2text = $this->center_function->convert($data['loan_amount']); //จำนวนเงินกู็(ตัวอักษร)
   $money_salary_2text = $this->center_function->convert($data['salary']);//เงินเดือน(ตัวอักษร)
   $start_member_year  = $this->center_function->diff_year($data['approve_date'],date('Y-m-d H:i:s')); // ปีที่เริ่มทำงาน (จำนวนปี)
   $start_member_month       = $this->center_function->diff_month_interval($data['approve_date'],date('Y-m-d H:i:s')); // จำนวนเดือน
   if ($data['approve_date'] != ''){
       $date_to_year       = (substr($data['approve_date'], 0, 4))+543; // ปีที่เริ่มทำสัญญา
   }
   $date_to_text       = number_format(substr($data['approve_date'], 8, 2)); // วันที่เริ่มทำสัญญา
   $date_to_month      = number_format(substr($data['approve_date'], 5, 2)); // เดือนที่เริ้มทำสัญญา
   $month2text         = $monthtext[$date_to_month]; // เดือนที่เริ่มทำสัญญา (ตัวอักษร)
   $full_date          = $date_to_text."  ".$month2text."  ".$date_to_year; // วัน:เดือน:ปี ที่เริ่มทำสัญญา
   if ($data['createdatetime'] != ''){
       $create_year       = (substr($data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
   }
   $create_day = number_format(substr($data['createdatetime'], 8, 2)); // วันที่บันทึกข้อมูล
   $create_month = number_format(substr($data['createdatetime'], 5, 2)); // เดือนที่บันทึกข้อมูล
   $create_month2text = $monthtext[$create_month]; // เดือนที่บันทึกข้อมูล(ตัวอักษร)
   if ($data['createdatetime'] != ''){
       $create_year       = (substr($data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
   }
   $day_start_period   = number_format(substr($data['date_start_period'], 8, 2));// วันเริ่มจ่ายงวด(หุ้น)
   $month_start_period = number_format(substr($data['date_start_period'], 5, 2)); // เดือนที่จ่ายค่างวด(หุ้น)
   $year_start_period  = (substr($data['approve_date'], 0, 4))+543; // ปีที่จ่ายค่างวด(หุ้น)
   $full_start_period  = $day_start_period."  ".$month_start_period."  ".$year_start_period; // วัน, เดือน, ปี ที่จ่ายค่างวด(หุ้น)
   $fullname_th        = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
   $contract_number_font = substr($data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
   $contract_number_back = substr($data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ
?>