<?php
$search_value=$_POST["search"];
$con=new mysqli($servername,$username,$password,$dbname);
if($con->connect_error){
    echo 'Connection Faild: '.$con->connect_error;
    }else{
        $sql="select title, url, description from sites where description OR url OR title like '%$search_value%'";

        $res=$link->query($sql);

        while($row=$res->fetch_assoc()){
          echo '\ntitle:  '.$row["title"];
          echo '\nurl:  '.$row["url"];
          echo '\nmeta:  '.$row["description"];

            }       

        }
?>