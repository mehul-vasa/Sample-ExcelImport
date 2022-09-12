<?php
//READ INSTRUCTIONS IN README.MD FILE.
include('../includes/config.php');
include('../includes/session.php');

//------------------------CODE TO UPLOAD FILE STARTS------------------------
//TODO CHANGE-1
//Directory where file has to be uploaded.
$target_dir = "";
//Upload File path
$target_file = $target_dir . basename($_FILES["uploadedExcel"]["name"]);
//Uploaded file name.
$fileName = basename( $_FILES["uploadedExcel"]["name"]);
//Status to check upload process.
$uploadOk = 1;
//To delete the uploaded excel file after importing data.
$deleteFileAfterImporting = true;
//Find uploaded file type.
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if file is uploaded or not.
if(isset($_FILES["uploadedExcel"])) {
    // Check if uploaded file has desired extension (in this case, xlsx)
    if($imageFileType != "xlsx") {
        $_SESSION['errorMsg_addNewQuestion'] = "Sorry, only .xlsx files are allowed.";
        header('location:../addStock.php');
        exit;
        $uploadOk = 0;
    } else {
        //If file is of desired format, upload to target Directory.
        if (move_uploaded_file($_FILES["uploadedExcel"]["tmp_name"], $target_file)) {
            $uploadOk = 1;
        } else {
          //If upload fails, show error leave current page.
            $_SESSION['errorMsg_addNewQuestion'] = "Sorry, there was an error uploading your file.";
            header('location:../addStock.php');
            exit;
        }
    }

} else {
  //Uploaded file type is not supported.
  $_SESSION['errorMsg_addNewQuestion'] = "Please upload excel file.";
  header('location:../addStock.php');
  exit;
}

//------------------------CODE TO UPLOAD FILE ENDS------------------------

//------------------------CODE TO PROCESS FILE ENDS------------------------

//Check if upload status is OK
if($uploadOk == 1) {
  //Import PhpExcel Library.
  set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
  include 'PHPExcel/IOFactory.php';

  // This is the file path to be uploaded.
  $inputFileName = $fileName;

  //Read file and save data to $objPHPExcel variable.
  try {
   $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
  } catch(Exception $e) {
   // die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
    $_SESSION['errorMsg_addNewQuestion'] = "Error loading file";
    header('location:../addStock.php');
    exit;
  }

  //Fetch all data of active sheet.
  $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
  //Fetch total count of row in that Excel sheet
  $arrayCount = count($allDataInSheet);

  //DECLARE HEADERS OF FILE.
  //TODO CHANGE-2
  $aHeader = trim($allDataInSheet[1]["A"]);
  $bHeader = trim($allDataInSheet[1]["B"]);
  $cHeader = trim($allDataInSheet[1]["C"]);
  $dHeader = trim($allDataInSheet[1]["D"]);
  $eHeader = trim($allDataInSheet[1]["E"]);

  //If file has header
  if($_REQUEST['header'] == 'Yes') {

    //Validate template file.
    //TODO CHANGE-3
    if($aHeader == "PRODUCT ID" && $bHeader == "WEIGHT" &&
        $cHeader == "REMARK") {
          insertData($allDataInSheet, $arrayCount, 2);
          //Index is 2 because in 1st index, headers are there and we don't want to insert them.
    } else {
      $_SESSION['errorMsg_addNewQuestion'] = "Template Mismatch";
      header('location:../addStock.php');
      exit;
    }

  } else {
    //If file do not have header.
    insertData($allDataInSheet, $arrayCount, 1);
  }

  //Delete the uploaded file.
  if($deleteFileAfterImporting) {
    if($dataInserted == 1) {
      unlink($fileName);
      $_SESSION['message_addNewQuestion'] = "Stock Added Successfully!!";
      header('location:../addStock.php');
      exit;
    } else {
      $_SESSION['errorMsg_addNewQuestion'] = "Unable To Add Stock. Please Try Again";
      header('location:../addStock.php');
      exit;
    }
  }


}

function insertData($allDataInSheet, $arrayCount, $index) {

  $dataInserted = 0;

  for($i=$index;$i<=$arrayCount;$i++){

    //Declare variables as per data in excel sheet.
    //TODO CHANGE-4
    $dataOfColumnA = trim($allDataInSheet[$i]["A"]); //Product id
    $dataOfColumnB = trim($allDataInSheet[$i]["B"]); //Product Weight
    $dataOfColumnC = trim($allDataInSheet[$i]["C"]); //Remark
    $dataOfColumnD = trim($allDataInSheet[$i]["D"]); //HUID
    $dataOfColumnE = trim($allDataInSheet[$i]["E"]); //Purchased By
    $dataOfColumnF = trim($allDataInSheet[$i]['F']); //Product Category

    //Check if variables are not empty.
    if($dataOfColumnA != "" && $dataOfColumnB != "" && $dataOfColumnF != "") {

      //Make insert Query
      //TODO CHANGE-5
      $insertQuery = "INSERT INTO `stock` (`productId`, `productWeight`, `huid`, `remark`,
                      `purchased_by`, `category`, `purchasedOn`, `status`) VALUES
                      ('".$dataOfColumnA."', '".$dataOfColumnB."', '".$dataOfColumnD."', '".$dataOfColumnC."',
                        '".$dataOfColumnE."', '".$dataOfColumnF."', 'active') ";

      $insertResult = mysqli_query($con, $insertQuery);

      if($insertResult) {
        $dataInserted = 1;
      } else {
        $dataInserted = 0;
      }

    } else {
      $_SESSION['errorMsg_addNewQuestion'] = "Invalid Data in File";
      header('location:../addStock.php');
      exit;
    }

  }

}

?>
