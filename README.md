## INSTRUCTIONS TO IMPORT DATA FROM EXCEL TO MYSQL DATABASE

`
CHANGE 1 : Edit variable for file upload as per need.
`
* $target_dir -> Directory where file has to be uploaded.
* $target_file -> Path of file to be uploaded with base-name of file.
* $fileName -> Name of the file.
* $deleteFileAfterImporting -> If you want to delete the excel file uploaded once the import functions are completed, make this varibale true, else make false.

`
CHANGE 2 : Declare headers present in file.
`

Headers are the heading in the excel file, mentioning the name or abbreviation of the data in that particular column.
These headers can be optional, please check particular use case.

`
CHANGE 3 : Validate Template File by checking Header's Text.
`

If headers are present in file, then validate the template file by checking the text in header.  
In this example, only few of the important headers are checked, User can check every header also as per requirement.   

```php
if($aHeader == "PRODUCT ID" && $bHeader == "WEIGHT" &&
    $cHeader == "REMARK") {
      //TEMPLATE FILE CHECKED AND VALIDATED.
}
```
Here, **$aHeader** has the text **"PRODUCT ID"** in excel file, so
```php
$aHeader = "PRODUCT ID"
```
is checked. In same way, all other headers can be checked as per the template file.

`
CHANGE 4 : Declare variable as per the data in file.
`

Declare all the variables in file, which has to be inserted in Database.
```php
  $dataOfColumnA = trim($allDataInSheet[1]["A"]);
```

In this example, Data from column A's 1st row will be stored in variable `$dataOfColumnA`  

`
CHANGE 5 : Create INSERT QUERY.
`

Create insert query for inserting data into Database with all the required variables and data.
